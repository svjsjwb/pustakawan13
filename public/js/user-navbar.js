document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENT REFERENCES
    ========================================================= */

    const account = document.querySelector('.user-navbar-account');
    const profileButton = document.getElementById('userNavbarProfile');
    const dropdown = document.getElementById('userNavbarDropdown');

    const notifWrapper = document.getElementById('userNavbarNotifWrapper');
    const notifBtn = document.getElementById('userNavbarNotifBtn');
    const notifMarkAll = document.getElementById('notifMarkAll');

    const openProfileModalBtn = document.getElementById('openProfileModalBtn');
    const profileModal = document.getElementById('userProfileModal');
    const profileModalOverlay = document.getElementById('userProfileModalOverlay');
    const profileModalClose = document.getElementById('userProfileModalClose');

    const profileForm = document.getElementById('userProfileForm');
    const profilePhotoInput = document.getElementById('profilePhotoInput');
    const modalAvatarPreview = document.getElementById('userModalAvatarPreview');

    const profileSaveBtn = document.getElementById('userProfileSaveBtn');
    const profileSaveToast = document.getElementById('userProfileSaveToast');
    const profileInputName = document.getElementById('profileInputName');
    const profileInputEmail = document.getElementById('profileInputEmail');
    const profileInputPhone = document.getElementById('profileInputPhone');
    const profileInputLocation = document.getElementById('profileInputLocation');

    const nameDisplays = document.querySelectorAll('.user-profile-name-display, .user-navbar-name');
    const emailDisplays = document.querySelectorAll('.user-profile-email-display');
    const avatarDisplays = document.querySelectorAll('.user-profile-avatar-display');

    const notifBadge = document.getElementById('userNotifBadge');
    const notifPopover = document.getElementById('userNotifPopover');
    const notifText = document.getElementById('userNotifText');
    const notifOpts = document.querySelectorAll('.notif-opt');

    const authUserId = account ? account.getAttribute('data-user-id') : null;
    const STORAGE_KEY = authUserId && authUserId !== '0' ? `pustakawan_user_profile_data_${authUserId}` : null;

    // Bersihkan key lama yang tidak ber-scope user agar tidak menimpa profil lintas sesi
    try {
        localStorage.removeItem('pustakawan_user_profile_data');
    } catch (e) {}

    /* =========================================================
       0. LOAD SAVED PROFILE FROM LOCAL STORAGE (PERSISTENCE)
    ========================================================= */

    function loadCachedProfile() {
        if (!STORAGE_KEY) return;
        try {
            const cached = localStorage.getItem(STORAGE_KEY);
            if (cached) {
                const data = JSON.parse(cached);
                if (data.name) {
                    nameDisplays.forEach(el => {
                        if (!el.textContent || el.textContent.trim() === '' || el.textContent.trim() === 'Pengguna') {
                            el.textContent = data.name;
                        }
                    });
                    if (profileInputName && !profileInputName.value) profileInputName.value = data.name;
                }
                if (data.email) {
                    emailDisplays.forEach(el => {
                        if (!el.textContent || el.textContent.trim() === '') {
                            el.textContent = data.email;
                        }
                    });
                    if (profileInputEmail && !profileInputEmail.value) profileInputEmail.value = data.email;
                }
                if (data.phone && profileInputPhone && !profileInputPhone.value) {
                    profileInputPhone.value = data.phone;
                }
                if (data.location && profileInputLocation && !profileInputLocation.value) {
                    profileInputLocation.value = data.location;
                }
                if (data.avatar) {
                    avatarDisplays.forEach(function (img) {
                        img.src = data.avatar;
                        img.style.display = 'block';
                    });
                }
            }
        } catch (e) {
            console.error('Error loading cached profile:', e);
        }
    }

    loadCachedProfile();


    /* =========================================================
       1. NAVBAR NOTIFICATION TOGGLE (SAMPING KIRI PROFILE)
    ========================================================= */

    if (notifWrapper && notifBtn) {
        notifBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            if (account) account.classList.remove('open');
            const isOpen = notifWrapper.classList.toggle('open');
            notifBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    if (notifMarkAll) {
        notifMarkAll.addEventListener('click', function (e) {
            e.stopPropagation();

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                const unreadItems = document.querySelectorAll('.notif-item.unread');
                unreadItems.forEach(item => item.classList.remove('unread'));
                const badge = document.getElementById('notifCountBadge') || document.querySelector('.notif-count-badge');
                if (badge) badge.textContent = '0 Baru';
                const dot = document.getElementById('userNotifDot') || document.querySelector('.user-notif-dot');
                if (dot) dot.style.display = 'none';
                if (notifMarkAll) notifMarkAll.style.opacity = '0.5';
            })
            .catch(err => console.debug('Mark all read error:', err));
        });
    }


    /* =========================================================
       2. DROPDOWN TOGGLE (FRAME 1000006625)
    ========================================================= */

    if (account && profileButton) {
        profileButton.addEventListener('click', function (event) {
            event.stopPropagation();
            if (notifWrapper) notifWrapper.classList.remove('open');
            const isOpen = account.classList.toggle('open');
            profileButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (notifPopover) notifPopover.classList.remove('open');
        });
    }


    /* =========================================================
       3. NOTIFICATION ALLOW/MUTE TOGGLE IN DROPDOWN
    ========================================================= */

    if (notifBadge && notifPopover) {
        notifBadge.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPopover.classList.toggle('open');
        });

        notifOpts.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const val = this.getAttribute('data-val');
                if (notifText) notifText.textContent = val;

                notifOpts.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                notifPopover.classList.remove('open');

                // Sinkronkan toggle switch di profile modal jika terbuka
                const profileToggleNotif = document.getElementById('profileToggleNotification');
                if (profileToggleNotif) {
                    profileToggleNotif.checked = (val.toLowerCase() === 'allow');
                    const slider = profileToggleNotif.nextElementSibling;
                    if (slider) slider.style.backgroundColor = profileToggleNotif.checked ? '#0f4c4c' : '#cbd5e1';
                }

                // Kirim perubahan persisten ke backend via AJAX
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                fetch('/user/toggle-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ value: val })
                })
                .then(res => res.json())
                .catch(err => console.debug('Toggle notification error:', err));
            });
        });
    }


    /* =========================================================
       4. FLOATING PROFILE MODAL & TAB SYSTEM
    ========================================================= */

    const openSettingsModalBtn = document.getElementById('openSettingsModalBtn');
    const tabBtns = document.querySelectorAll('.profile-tab-btn');
    const tabPanes = document.querySelectorAll('.profile-tab-pane');

    function switchTab(targetTabId) {
        tabBtns.forEach(function (btn) {
            const isMatch = btn.getAttribute('data-tab') === targetTabId;
            btn.classList.toggle('active', isMatch);
            btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
        });

        tabPanes.forEach(function (pane) {
            if (pane.id === targetTabId) {
                pane.style.display = 'block';
                pane.classList.add('active');
            } else {
                pane.style.display = 'none';
                pane.classList.remove('active');
            }
        });
    }

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.getAttribute('data-tab');
            if (target) switchTab(target);
        });
    });

    function openModal(initialTab) {
        if (!profileModal) return;
        if (account) {
            account.classList.remove('open');
            if (profileButton) profileButton.setAttribute('aria-expanded', 'false');
        }
        if (notifWrapper) notifWrapper.classList.remove('open');
        profileModal.classList.add('open');
        profileModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        if (initialTab) {
            switchTab(initialTab);
        }
    }

    function closeModal() {
        if (!profileModal) return;
        profileModal.classList.remove('open');
        profileModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (openProfileModalBtn) {
        openProfileModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openModal('tabProfile');
        });
    }

    if (openSettingsModalBtn) {
        openSettingsModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            openModal('tabPreferences');
        });
    }

    if (profileModalClose) {
        profileModalClose.addEventListener('click', function (e) {
            e.preventDefault();
            closeModal();
        });
    }

    if (profileModalOverlay) {
        profileModalOverlay.addEventListener('click', function (e) {
            e.preventDefault();
            closeModal();
        });
    }


    /* =========================================================
       5. ITEM 1: TAB PROFIL SAYA (EDIT PROFILE & AVATAR)
    ========================================================= */

    let currentAvatarBase64 = null;
    const profileErrorAlert = document.getElementById('profileErrorAlert');

    function showProfileError(msg) {
        if (profileErrorAlert) {
            profileErrorAlert.textContent = msg;
            profileErrorAlert.style.display = 'block';
        }
    }

    function hideProfileError() {
        if (profileErrorAlert) {
            profileErrorAlert.style.display = 'none';
            profileErrorAlert.textContent = '';
        }
    }

    if (profilePhotoInput) {
        profilePhotoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    showProfileError('Format gambar harus JPG atau PNG.');
                    profilePhotoInput.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showProfileError('Ukuran file foto profil maksimal 2MB.');
                    profilePhotoInput.value = '';
                    return;
                }
                hideProfileError();

                const reader = new FileReader();
                reader.onload = function (event) {
                    currentAvatarBase64 = event.target.result;
                    avatarDisplays.forEach(function (img) {
                        img.src = currentAvatarBase64;
                        img.style.display = 'block';
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (profileSaveBtn && profileForm) {
        profileSaveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            hideProfileError();

            const newName = profileInputName ? profileInputName.value.trim() : '';
            const newPhone = profileInputPhone ? profileInputPhone.value.trim() : '';
            const newLocation = profileInputLocation ? profileInputLocation.value.trim() : '';

            if (!newName) {
                showProfileError('Nama lengkap wajib diisi.');
                return;
            }

            // Validasi format nomor telepon
            if (newPhone) {
                const phoneRegex = /^(\+?[0-9\s\-\(\)]){8,20}$/;
                if (!phoneRegex.test(newPhone)) {
                    showProfileError('Nomor telepon tidak valid (contoh: 08123456789 atau +628123456789).');
                    return;
                }
            }

            // Validasi file avatar jika ada
            if (profilePhotoInput && profilePhotoInput.files[0]) {
                const file = profilePhotoInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    showProfileError('Format gambar harus JPG atau PNG.');
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showProfileError('Ukuran file gambar maksimal 2MB.');
                    return;
                }
            }

            // Update UI immediately
            nameDisplays.forEach(el => el.textContent = newName);

            // Cache in LocalStorage
            if (STORAGE_KEY) {
                try {
                    const cachedData = {
                        name: newName,
                        email: profileInputEmail ? profileInputEmail.value : '',
                        phone: newPhone,
                        location: newLocation,
                        avatar: currentAvatarBase64 || (avatarDisplays[0] ? avatarDisplays[0].src : null),
                    };
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(cachedData));
                } catch (err) {
                    console.warn('LocalStorage error:', err);
                }
            }

            // Loading state
            const btnText = profileSaveBtn.querySelector('.save-btn-text');
            const btnSpinner = profileSaveBtn.querySelector('.save-btn-spinner');
            if (btnText && btnSpinner) {
                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-flex';
            }

            // Send to Backend
            const formData = new FormData(profileForm);
            const profileToggleNotif = document.getElementById('profileToggleNotification');
            if (profileToggleNotif) {
                formData.set('is_notification_enabled', profileToggleNotif.checked ? '1' : '0');
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch(profileForm.action || '/profile/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(result => {
                if (btnText && btnSpinner) {
                    btnText.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }

                if (result.ok && result.body.success) {
                    hideProfileError();
                    if (result.body.user) {
                        if (result.body.user.name) nameDisplays.forEach(el => el.textContent = result.body.user.name);
                        if (result.body.user.avatar_url) {
                            avatarDisplays.forEach(function (img) {
                                img.src = result.body.user.avatar_url;
                                img.style.display = 'block';
                            });
                        }
                    }

                    if (profileSaveToast) {
                        profileSaveToast.classList.add('show');
                        setTimeout(function () {
                            profileSaveToast.classList.remove('show');
                        }, 3000);
                    }
                } else {
                    let errMsg = result.body.message || 'Gagal memperbarui profil.';
                    if (result.body.errors) {
                        const firstKey = Object.keys(result.body.errors)[0];
                        if (firstKey && result.body.errors[firstKey].length) {
                            errMsg = result.body.errors[firstKey][0];
                        }
                    }
                    showProfileError(errMsg);
                }
            })
            .catch(err => {
                console.warn('Backend update failed:', err);
                if (btnText && btnSpinner) {
                    btnText.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }
                showProfileError('Koneksi terganggu. Profil sementara disimpan lokal.');
            });
        });
    }


    /* =========================================================
       6. ITEM 2: TAB KEAMANAN (PASSWORD UPDATE)
    ========================================================= */

    // Toggle Password Visibility (Eye icon)
    document.querySelectorAll('.toggle-password-visibility').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const inputWrap = this.closest('.password-input-wrap');
            if (!inputWrap) return;
            const input = inputWrap.querySelector('input');
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');

            if (input && input.type === 'password') {
                input.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = 'block';
            } else if (input) {
                input.type = 'password';
                if (eyeOpen) eyeOpen.style.display = 'block';
                if (eyeClosed) eyeClosed.style.display = 'none';
            }
        });
    });

    const userSecurityForm = document.getElementById('userSecurityForm');
    const userSecuritySaveBtn = document.getElementById('userSecuritySaveBtn');
    const userSecuritySaveToast = document.getElementById('userSecuritySaveToast');
    const securityErrorAlert = document.getElementById('securityErrorAlert');
    const securityCurrentPassword = document.getElementById('securityCurrentPassword');
    const securityNewPassword = document.getElementById('securityNewPassword');
    const securityConfirmPassword = document.getElementById('securityConfirmPassword');

    function showSecurityError(msg) {
        if (securityErrorAlert) {
            securityErrorAlert.textContent = msg;
            securityErrorAlert.style.display = 'block';
        }
    }

    function hideSecurityError() {
        if (securityErrorAlert) {
            securityErrorAlert.style.display = 'none';
            securityErrorAlert.textContent = '';
        }
    }

    if (userSecuritySaveBtn && userSecurityForm) {
        userSecuritySaveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            hideSecurityError();

            const currentPass = securityCurrentPassword ? securityCurrentPassword.value : '';
            const newPass = securityNewPassword ? securityNewPassword.value : '';
            const confirmPass = securityConfirmPassword ? securityConfirmPassword.value : '';

            if (!currentPass) {
                showSecurityError('Password saat ini wajib diisi.');
                return;
            }

            if (!newPass) {
                showSecurityError('Password baru wajib diisi.');
                return;
            }

            // Validasi minimal 8 karakter
            if (newPass.length < 8) {
                showSecurityError('Password baru minimal harus 8 karakter.');
                return;
            }

            // Validasi kombinasi huruf & angka
            const hasLetter = /[a-zA-Z]/.test(newPass);
            const hasNumber = /[0-9]/.test(newPass);
            if (!hasLetter || !hasNumber) {
                showSecurityError('Password baru harus merupakan kombinasi huruf dan angka.');
                return;
            }

            // Validasi match konfirmasi password
            if (newPass !== confirmPass) {
                showSecurityError('Konfirmasi password baru tidak cocok.');
                return;
            }

            // Loading state
            const btnText = userSecuritySaveBtn.querySelector('.save-btn-text');
            const btnSpinner = userSecuritySaveBtn.querySelector('.save-btn-spinner');
            if (btnText && btnSpinner) {
                btnText.style.display = 'none';
                btnSpinner.style.display = 'inline-flex';
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch(userSecurityForm.action || '/profile/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    current_password: currentPass,
                    new_password: newPass,
                    new_password_confirmation: confirmPass,
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
            .then(result => {
                if (btnText && btnSpinner) {
                    btnText.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }

                if (result.ok && result.body.success) {
                    hideSecurityError();
                    userSecurityForm.reset();

                    if (userSecuritySaveToast) {
                        userSecuritySaveToast.classList.add('show');
                        setTimeout(function () {
                            userSecuritySaveToast.classList.remove('show');
                        }, 3500);
                    }
                } else {
                    let errMsg = result.body.message || 'Gagal memperbarui kata sandi.';
                    if (result.body.errors) {
                        const firstKey = Object.keys(result.body.errors)[0];
                        if (firstKey && result.body.errors[firstKey].length) {
                            errMsg = result.body.errors[firstKey][0];
                        }
                    }
                    showSecurityError(errMsg);
                }
            })
            .catch(err => {
                console.error('Password update error:', err);
                if (btnText && btnSpinner) {
                    btnText.style.display = 'inline';
                    btnSpinner.style.display = 'none';
                }
                showSecurityError('Terjadi gangguan koneksi. Silakan coba lagi.');
            });
        });
    }


    /* =========================================================
       7. ITEM 3: TAB PREFERENSI (NOTIFIKASI & TEMA)
    ========================================================= */

    const prefNotifStatusLabel = document.getElementById('prefNotifStatusLabel');
    const prefToggleCaption = document.getElementById('prefToggleCaption');
    const prefSaveToast = document.getElementById('prefSaveToast');
    const profileToggleNotif = document.getElementById('profileToggleNotification');

    function showPrefToast(msg) {
        if (prefSaveToast) {
            prefSaveToast.textContent = msg ? '✓ ' + msg : '✓ Preferensi berhasil diperbarui!';
            prefSaveToast.style.opacity = '1';
            setTimeout(function () {
                prefSaveToast.style.opacity = '0';
            }, 2500);
        }
    }

    if (profileToggleNotif) {
        profileToggleNotif.addEventListener('change', function () {
            const isChecked = this.checked;
            const val = isChecked ? 'Allow' : 'Mute';

            // Update UI status label
            if (prefNotifStatusLabel) {
                prefNotifStatusLabel.textContent = isChecked ? 'Aktif' : 'Senyap';
            }
            if (prefToggleCaption) {
                prefToggleCaption.textContent = isChecked ? 'Notifikasi diizinkan' : 'Notifikasi dimatikan';
            }

            // Sync with dropdown notification badge text
            if (notifText) notifText.textContent = val;
            notifOpts.forEach(function (b) {
                if (b.getAttribute('data-val') === val) {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });

            // Sync toggle slider background
            const slider = this.nextElementSibling;
            if (slider) slider.style.backgroundColor = isChecked ? '#0f4c4c' : '#cbd5e1';

            // Send async update to backend
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch('/user/toggle-notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ value: val })
            })
            .then(res => res.json())
            .then(data => {
                showPrefToast(isChecked ? 'Notifikasi diaktifkan.' : 'Notifikasi dimatikan.');
            })
            .catch(err => {
                console.debug('Toggle notification error:', err);
                showPrefToast(isChecked ? 'Notifikasi diaktifkan.' : 'Notifikasi dimatikan.');
            });
        });
    }

    // THEME MODE SWITCHER (LIGHT / DARK)
    const THEME_KEY = 'pustakawan_theme';
    const themeOptionLight = document.getElementById('themeOptionLight');
    const themeOptionDark = document.getElementById('themeOptionDark');

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (themeOptionDark) themeOptionDark.classList.add('active');
            if (themeOptionLight) themeOptionLight.classList.remove('active');
        } else {
            document.documentElement.removeAttribute('data-theme');
            if (themeOptionLight) themeOptionLight.classList.add('active');
            if (themeOptionDark) themeOptionDark.classList.remove('active');
        }
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (e) {
            console.warn('LocalStorage error saving theme:', e);
        }
    }

    // Initialize saved theme on page load
    const currentTheme = localStorage.getItem(THEME_KEY) || 'light';
    applyTheme(currentTheme);

    if (themeOptionLight) {
        themeOptionLight.addEventListener('click', function () {
            applyTheme('light');
            showPrefToast('Mode Terang diaktifkan.');
        });
    }

    if (themeOptionDark) {
        themeOptionDark.addEventListener('click', function () {
            applyTheme('dark');
            showPrefToast('Mode Gelap diaktifkan.');
        });
    }


    /* =========================================================
       8. GLOBAL CLICK & KEY LISTENERS (OUTSIDE CLICK / ESC)
    ========================================================= */

    document.addEventListener('click', function (event) {
        if (account && !account.contains(event.target)) {
            account.classList.remove('open');
            if (profileButton) profileButton.setAttribute('aria-expanded', 'false');
        }
        if (notifWrapper && !notifWrapper.contains(event.target)) {
            notifWrapper.classList.remove('open');
            if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
        }
        if (notifPopover && notifBadge && !notifBadge.contains(event.target) && !notifPopover.contains(event.target)) {
            notifPopover.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (profileModal && profileModal.classList.contains('open')) {
                closeModal();
            } else {
                if (account) {
                    account.classList.remove('open');
                    if (profileButton) profileButton.setAttribute('aria-expanded', 'false');
                }
                if (notifWrapper) {
                    notifWrapper.classList.remove('open');
                    if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
                }
            }
        }
    });

});