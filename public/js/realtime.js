/**
 * Pustakawan Real-time Data Synchronization (Non-blocking Smart Polling & Event Handler)
 */
window.PustakawanRealtime = (function () {
    let lastEventId = 0;
    const listeners = {};
    let pollInterval = null;
    let isPolling = false;
    let activeAbortController = null;
    let eventSource = null;

    function init() {
        // Use lightweight, non-blocking polling as primary mechanism
        // to completely eliminate PHP worker thread starvation and slow page transitions
        startPolling();
    }

    function startPolling() {
        if (isPolling && pollInterval) return;
        isPolling = true;

        function doPoll() {
            if (!isPolling) return;
            // When document is hidden (user on another tab), pause polling to conserve resources
            if (document.hidden) return;

            if (activeAbortController) {
                try { activeAbortController.abort(); } catch (e) {}
            }
            activeAbortController = new AbortController();

            const url = '/events/poll' + (lastEventId ? '?last_id=' + lastEventId : '');
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: activeAbortController.signal
            })
            .then(res => {
                if (!res.ok) throw new Error('Poll response status: ' + res.status);
                return res.json();
            })
            .then(res => {
                if (res && res.last_id) {
                    lastEventId = res.last_id;
                }
                if (res && Array.isArray(res.events) && res.events.length > 0) {
                    res.events.forEach(function (evt) {
                        trigger(evt.event, evt.payload);
                    });
                }
            })
            .catch(err => {
                // Ignore aborted fetches or transient network issues
            });
        }

        // Run first poll shortly after DOM is ready, then recurring interval
        setTimeout(doPoll, 300);
        pollInterval = setInterval(doPoll, 2500);
    }

    function cleanup() {
        isPolling = false;
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
        if (activeAbortController) {
            try { activeAbortController.abort(); } catch (e) {}
            activeAbortController = null;
        }
        if (eventSource) {
            try { eventSource.close(); } catch (e) {}
            eventSource = null;
        }
    }

    // Immediately stop polling and abort in-flight requests when user navigates or reloads
    window.addEventListener('beforeunload', cleanup);
    window.addEventListener('pagehide', cleanup);

    // Adaptive polling: check immediately when user switches back to this tab
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && isPolling) {
            setTimeout(function () {
                if (isPolling && !document.hidden) {
                    const url = '/events/poll' + (lastEventId ? '?last_id=' + lastEventId : '');
                    fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.last_id) {
                            lastEventId = res.last_id;
                        }
                        if (res && Array.isArray(res.events) && res.events.length > 0) {
                            res.events.forEach(function (evt) {
                                trigger(evt.event, evt.payload);
                            });
                        }
                    })
                    .catch(() => {});
                }
            }, 100);
        }
    });

    function on(event, callback) {
        if (!listeners[event]) {
            listeners[event] = [];
        }
        listeners[event].push(callback);
    }

    function trigger(event, data) {
        if (listeners[event]) {
            listeners[event].forEach(function (cb) {
                try {
                    cb(data);
                } catch (e) {
                    console.error('Realtime listener error:', e);
                }
            });
        }
    }

    /**
     * UI Toast Notification
     */
    function toast(message, type = 'success', title = '') {
        let container = document.getElementById('pustakawan-realtime-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'pustakawan-realtime-toast-container';
            container.style.cssText = 'position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; max-width: 380px; width: calc(100% - 48px);';
            document.body.appendChild(container);
        }

        const el = document.createElement('div');
        el.style.cssText = 'pointer-events: auto; background: #ffffff; border-radius: 12px; padding: 14px 18px; box-shadow: 0 10px 30px rgba(15, 76, 76, 0.16); border-left: 5px solid ' + (type === 'success' ? '#059669' : '#0284c7') + '; display: flex; align-items: flex-start; gap: 12px; font-family: inherit; font-size: 13.5px; animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;';

        const iconSvg = type === 'success'
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';

        el.innerHTML = iconSvg +
            '<div style="flex: 1; min-width: 0;">' +
            (title ? '<div style="font-weight: 700; color: #1e293b; margin-bottom: 2px; font-size: 14px;">' + title + '</div>' : '') +
            '<div style="color: #475569; line-height: 1.4;">' + message + '</div>' +
            '</div>' +
            '<button type="button" style="background:none; border:none; color:#94a3b8; font-size:16px; cursor:pointer; padding:0 4px;" onclick="this.parentElement.remove();">×</button>';

        container.appendChild(el);

        setTimeout(function () {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-10px)';
            el.style.transition = 'all 0.3s ease';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    }

    // Auto initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        on: on,
        toast: toast,
        cleanup: cleanup,
        startPolling: startPolling,
    };
})();
