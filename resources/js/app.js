/* ============================= DATA ============================= */
const CATEGORIES = ['Fiksi','Non-Fiksi','Sains & Teknologi','Sejarah','Anak & Remaja','Ekonomi & Bisnis','Referensi'];
const SPINE_COLORS = ['#1E3A5F','#3F7D58','#B34632','#8f6524','#5A4A8F','#2E4256','#B8863B','#3B6E71'];

function spineColor(seed){ return SPINE_COLORS[seed % SPINE_COLORS.length]; }

let books = [
 {id:1,title:'Laut Bercerita',author:'Leila S. Chudori',isbn:'978-602-424-694-5',category:'Fiksi',publisher:'KPG',year:2017,stock:4,available:2,callno:'813.6 CHU l',shelf:'A-12'},
 {id:2,title:'Bumi Manusia',author:'Pramoedya Ananta Toer',isbn:'978-979-97312-1-2',category:'Fiksi',publisher:'Lentera Dipantara',year:1980,stock:5,available:0,callno:'813.6 PRA b',shelf:'A-13'},
 {id:3,title:'Sapiens: Riwayat Singkat Umat Manusia',author:'Yuval Noah Harari',isbn:'978-602-291-092-0',category:'Non-Fiksi',publisher:'KPG',year:2017,stock:3,available:1,callno:'909 HAR s',shelf:'B-04'},
 {id:4,title:'Filosofi Teras',author:'Henry Manampiring',isbn:'978-602-06-3082-4',category:'Non-Fiksi',publisher:'Kompas',year:2018,stock:6,available:3,callno:'188 MAN f',shelf:'B-05'},
 {id:5,title:'Cantik Itu Luka',author:'Eka Kurniawan',isbn:'978-979-91-0499-7',category:'Fiksi',publisher:'Gramedia',year:2002,stock:3,available:1,callno:'813.6 KUR c',shelf:'A-14'},
 {id:6,title:'Pengantar Ilmu Komputer',author:'Tim Penulis ITB',isbn:'978-602-8420-11-4',category:'Sains & Teknologi',publisher:'ITB Press',year:2019,stock:5,available:2,callno:'004 TIM p',shelf:'C-01'},
 {id:7,title:'Clean Code',author:'Robert C. Martin',isbn:'978-0-13-235088-4',category:'Sains & Teknologi',publisher:'Prentice Hall',year:2008,stock:4,available:0,callno:'005.1 MAR c',shelf:'C-02'},
 {id:8,title:'Sejarah Indonesia Modern',author:'M.C. Ricklefs',isbn:'978-979-461-909-1',category:'Sejarah',publisher:'Serambi',year:2008,stock:3,available:2,callno:'959.8 RIC s',shelf:'D-01'},
 {id:9,title:'Gadis Kretek',author:'Ratih Kumala',isbn:'978-979-1811-72-1',category:'Fiksi',publisher:'GPU',year:2012,stock:4,available:4,callno:'813.6 KUM g',shelf:'A-15'},
 {id:10,title:'Ekonomi Mikro Islam',author:'Adiwarman Karim',isbn:'978-979-769-105-8',category:'Ekonomi & Bisnis',publisher:'Rajawali Pers',year:2015,stock:3,available:1,callno:'330 KAR e',shelf:'E-02'},
 {id:11,title:'Si Kancil dan Petualangannya',author:'Tim Dongeng Nusantara',isbn:'978-602-16736-0-3',category:'Anak & Remaja',publisher:'Erlangga',year:2015,stock:8,available:6,callno:'398.2 TIM s',shelf:'F-01'},
 {id:12,title:'Kamus Besar Bahasa Indonesia',author:'Badan Bahasa',isbn:'978-979-22-0234-1',category:'Referensi',publisher:'Balai Pustaka',year:2016,stock:2,available:2,callno:'R 499.221 BAD k',shelf:'G-01'},
 {id:13,title:'Atomic Habits (Ed. Indonesia)',author:'James Clear',isbn:'978-602-06-4400-5',category:'Non-Fiksi',publisher:'Gramedia',year:2019,stock:6,available:0,callno:'158.1 CLE a',shelf:'B-06'},
 {id:14,title:'Negeri 5 Menara',author:'Ahmad Fuadi',isbn:'978-979-22-4861-5',category:'Fiksi',publisher:'GPU',year:2009,stock:5,available:3,callno:'813.6 FUA n',shelf:'A-16'},
 {id:15,title:'Design Patterns',author:'Erich Gamma dkk.',isbn:'978-0-201-63361-0',category:'Sains & Teknologi',publisher:'Addison-Wesley',year:1994,stock:2,available:1,callno:'005.1 GAM d',shelf:'C-03'},
 {id:16,title:'Rahasia Meede',author:'E.S. Ito',isbn:'978-979-22-3251-5',category:'Fiksi',publisher:'Hikmah',year:2007,stock:3,available:0,callno:'813.6 ITO r',shelf:'A-17'},
 {id:17,title:'Ilmu Kimia Dasar',author:'Prof. Achmad Zulfikar',isbn:'978-602-244-118-9',category:'Sains & Teknologi',publisher:'UI Press',year:2020,stock:4,available:2,callno:'540 ZUL i',shelf:'C-04'},
 {id:18,title:'Gajah Mada: Biografi Politik',author:'Langit Kresna Hariadi',isbn:'978-979-1032-88-1',category:'Sejarah',publisher:'Tiga Serangkai',year:2010,stock:3,available:1,callno:'959.8 HAR g',shelf:'D-02'},
];

let members = [
 {id:1,no:'AGT-0001',name:'Dimas Prasetyo',email:'dimas.p@mail.com',phone:'0812-3456-7801',type:'Mahasiswa',status:'aktif'},
 {id:2,no:'AGT-0002',name:'Siti Rahmawati',email:'siti.r@mail.com',phone:'0812-3456-7802',type:'Dosen / Staf',status:'aktif'},
 {id:3,no:'AGT-0003',name:'Bagus Wicaksono',email:'bagus.w@mail.com',phone:'0812-3456-7803',type:'Umum',status:'aktif'},
 {id:4,no:'AGT-0004',name:'Nadia Putri',email:'nadia.putri@mail.com',phone:'0812-3456-7804',type:'Pelajar',status:'aktif'},
 {id:5,no:'AGT-0005',name:'Fajar Nugroho',email:'fajar.n@mail.com',phone:'0812-3456-7805',type:'Mahasiswa',status:'nonaktif'},
 {id:6,no:'AGT-0006',name:'Intan Permatasari',email:'intan.p@mail.com',phone:'0812-3456-7806',type:'Umum',status:'aktif'},
 {id:7,no:'AGT-0007',name:'Rizky Ramadhan',email:'rizky.r@mail.com',phone:'0812-3456-7807',type:'Mahasiswa',status:'aktif'},
 {id:8,no:'AGT-0008',name:'Wulan Sari',email:'wulan.sari@mail.com',phone:'0812-3456-7808',type:'Dosen / Staf',status:'aktif'},
 {id:9,no:'AGT-0009',name:'Yusuf Hidayat',email:'yusuf.h@mail.com',phone:'0812-3456-7809',type:'Pelajar',status:'nonaktif'},
 {id:10,no:'AGT-0010',name:'Citra Ayu Lestari',email:'citra.a@mail.com',phone:'0812-3456-7810',type:'Umum',status:'aktif'},
];

function daysFromNow(n){ const d=new Date(); d.setDate(d.getDate()+n); return d.toISOString().slice(0,10); }
function fmtDate(s){ const d=new Date(s); return d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }
function diffDays(a,b){ return Math.round((new Date(b)-new Date(a))/86400000); }
function rupiah(n){ return 'Rp' + n.toLocaleString('id-ID'); }
const DENDA_PER_HARI = 2000;
const TODAY = daysFromNow(0);

let loans = [
 {id:1,memberId:1,bookId:2,loanDate:daysFromNow(-20),dueDate:daysFromNow(-6),returnDate:null},
 {id:2,memberId:2,bookId:7,loanDate:daysFromNow(-15),dueDate:daysFromNow(-1),returnDate:null},
 {id:3,memberId:3,bookId:13,loanDate:daysFromNow(-25),dueDate:daysFromNow(-11),returnDate:null},
 {id:4,memberId:4,bookId:16,loanDate:daysFromNow(-10),dueDate:daysFromNow(4),returnDate:null},
 {id:5,memberId:6,bookId:3,loanDate:daysFromNow(-5),dueDate:daysFromNow(9),returnDate:null},
 {id:6,memberId:7,bookId:5,loanDate:daysFromNow(-3),dueDate:daysFromNow(11),returnDate:null},
 {id:7,memberId:8,bookId:1,loanDate:daysFromNow(-2),dueDate:daysFromNow(1),returnDate:null},
 {id:8,memberId:1,bookId:10,loanDate:daysFromNow(-30),dueDate:daysFromNow(-16),returnDate:daysFromNow(-14)},
 {id:9,memberId:2,bookId:6,loanDate:daysFromNow(-40),dueDate:daysFromNow(-26),returnDate:daysFromNow(-25)},
 {id:10,memberId:9,bookId:18,loanDate:daysFromNow(-12),dueDate:daysFromNow(2),returnDate:null},
 {id:11,memberId:10,bookId:1,loanDate:daysFromNow(-8),dueDate:daysFromNow(6),returnDate:null},
 {id:12,memberId:3,bookId:9,loanDate:daysFromNow(-45),dueDate:daysFromNow(-31),returnDate:daysFromNow(-33)},
];

let reservations = [
 {id:1,memberId:5,bookId:2,date:daysFromNow(-3),status:'menunggu'},
 {id:2,memberId:9,bookId:13,date:daysFromNow(-1),status:'menunggu'},
 {id:3,memberId:4,bookId:7,date:daysFromNow(-6),status:'siap'},
];

let events = [
 {id:1,date:daysFromNow(2),title:'Bedah Buku: Laut Bercerita',type:'Diskusi'},
 {id:2,date:daysFromNow(5),title:'Pelatihan Literasi Digital',type:'Workshop'},
 {id:3,date:daysFromNow(9),title:'Story Telling Anak',type:'Komunitas'},
 {id:4,date:daysFromNow(14),title:'Kunjungan Sekolah Dasar 02',type:'Kunjungan'},
];

let nextBookId = 19, nextMemberId = 11, nextLoanId = 13, nextResId = 4;
let catalogView = 'grid';
let loanFilter = '';
let settingsTab = 'general';

/* ============================= HELPERS ============================= */
function loanStatus(loan){
  if(loan.returnDate) return 'kembali';
  return new Date(loan.dueDate) < new Date(TODAY) ? 'terlambat' : 'aktif';
}
function memberById(id){ return members.find(m=>m.id===id); }
function bookById(id){ return books.find(b=>b.id===id); }
function activeLoansForBook(bookId){ return loans.filter(l=>l.bookId===bookId && !l.returnDate); }
function activeLoanCountForMember(memberId){ return loans.filter(l=>l.memberId===memberId && !l.returnDate).length; }
function memberFine(memberId){
  return loans.filter(l=>l.memberId===memberId).reduce((sum,l)=>sum+calcFine(l),0);
}
function calcFine(loan){
  const end = loan.returnDate || TODAY;
  const late = diffDays(loan.dueDate, end);
  return late > 0 ? late*DENDA_PER_HARI : 0;
}
function bookIcon(){
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>';
}

/* ============================= NAV ============================= */
const PAGE_META = {
  dashboard:{title:'Dasbor', eyebrow:'Ringkasan Perpustakaan'},
  catalog:{title:'Katalog Publik', eyebrow:'Koleksi'},
  books:{title:'Manajemen Buku', eyebrow:'Koleksi'},
  circulation:{title:'Peminjaman', eyebrow:'Sirkulasi'},
  reservations:{title:'Reservasi', eyebrow:'Sirkulasi'},
  fines:{title:'Denda & Pembayaran', eyebrow:'Sirkulasi'},
  members:{title:'Anggota', eyebrow:'Komunitas'},
  calendar:{title:'Kegiatan Perpustakaan', eyebrow:'Komunitas'},
  reports:{title:'Laporan & Statistik', eyebrow:'Analitik'},
  settings:{title:'Pengaturan', eyebrow:'Sistem'},
};

function nav(page){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById('page-'+page).classList.add('active');
  document.querySelectorAll('.nav-pill').forEach(n=>n.classList.remove('active'));
  const activePill = document.querySelector('.nav-pill[data-page="'+page+'"]');
  if(activePill) {
    activePill.classList.add('active');
    activePill.scrollIntoView({behavior:'smooth', inline:'center', block:'nearest'});
  }
  document.getElementById('page-title').textContent = PAGE_META[page].title;
  document.getElementById('page-eyebrow').textContent = PAGE_META[page].eyebrow;
  const cta = document.getElementById('topbar-cta');
  if(page==='books'){ cta.style.display='flex'; cta.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>Tambah Buku'; cta.onclick=()=>openBookModal(); }
  else if(page==='members'){ cta.style.display='flex'; cta.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>Tambah Anggota'; cta.onclick=()=>openMemberModal(); }
  else { cta.style.display='none'; }
  renderAll();
}

function globalSearch(v){
  if(!v) return;
  nav('catalog');
  document.getElementById('cat-search').value = v;
  renderCatalog();
}

/* ============================= RENDER ALL ============================= */
function renderAll(){
  renderDashboard();
  populateCategorySelects();
  renderCatalog();
  renderBooksTable();
  populateLoanForm();
  renderLoans();
  renderReservations();
  renderFines();
  renderMembers();
  renderCalendar();
  renderReports();
  renderSettings();
}

/* ============================= RENDER: DASHBOARD ============================= */
function renderDashboard(){
  const totalBooks = books.reduce((s,b)=>s+b.stock,0);
  const totalTitles = books.length;
  const activeLoans = loans.filter(l=>!l.returnDate).length;
  const overdue = loans.filter(l=>loanStatus(l)==='terlambat').length;
  const activeMembers = members.filter(m=>m.status==='aktif').length;

  const stats = [
    {label:'Total Koleksi', num:totalBooks+' eks.', sub:totalTitles+' judul', icon:bookIcon(), color:'var(--primary)', bg:'var(--primary-soft)', delta:'+12 bulan ini', trend:'up'},
    {label:'Anggota Aktif', num:activeMembers, sub:members.length+' total terdaftar', icon:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>', color:'var(--success)', bg:'var(--success-soft)', delta:'+3 minggu ini', trend:'up'},
    {label:'Sedang Dipinjam', num:activeLoans, sub:'Buku beredar', icon:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 2l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>', color:'#8f6524', bg:'var(--brass-soft)', delta:'stabil', trend:'flat'},
    {label:'Terlambat', num:overdue, sub:'Butuh tindak lanjut', icon:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/></svg>', color:'var(--danger)', bg:'var(--danger-soft)', delta:overdue+' perlu ditagih', trend:'down'},
    {label:'Reservasi Aktif', num:reservations.filter(r=>r.status!=='selesai').length, sub:'Dalam antrean', icon:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>', color:'var(--primary)', bg:'var(--primary-soft)', delta:'baru', trend:'flat'},
  ];
  document.getElementById('stat-cards').innerHTML = stats.map(s=>`
    <div class="card stat-card">
      <div class="stamp"><span>Lestari<br>Pustaka</span></div>
      <div class="stat-top">
        <div class="stat-icon" style="background:${s.bg};color:${s.color};">${s.icon}</div>
      </div>
      <div class="stat-num">${s.num}</div>
      <div class="stat-label">${s.label} · ${s.sub}</div>
      <span class="stat-delta ${s.trend}">${s.delta}</span>
    </div>`).join('');

  const dist = [4,7,5,9,6,3,5];
  const days = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
  const max = Math.max(...dist);
  document.getElementById('chart-bars').innerHTML = dist.map((v,i)=>`
    <div class="bar-col">
      <div class="bar-track" style="align-items:flex-end;">
        <div class="bar-fill ${i===dist.length-1?'brass':''}" style="height:${(v/max*100)}px;"></div>
      </div>
      <div class="bar-day">${days[i]}</div>
    </div>`).join('');

  const acts = loans.slice().sort((a,b)=> (b.returnDate||b.loanDate) > (a.returnDate||a.loanDate) ? 1:-1).slice(0,6).map(l=>{
    const m = memberById(l.memberId), b = bookById(l.bookId);
    const returned = !!l.returnDate;
    return {icon: returned? '↩':'📖', color: returned?'var(--success)':'var(--primary)', bg: returned?'var(--success-soft)':'var(--primary-soft)',
      text: returned ? `<b>${m.name}</b> mengembalikan <b>${b.title}</b>` : `<b>${m.name}</b> meminjam <b>${b.title}</b>`,
      time: fmtDate(returned?l.returnDate:l.loanDate)};
  });
  document.getElementById('activity-list').innerHTML = acts.map(a=>`
    <div class="activity-item">
      <div class="act-icon" style="background:${a.bg};color:${a.color};font-size:13px;">${a.icon}</div>
      <div><div class="act-text">${a.text}</div><div class="act-time">${a.time}</div></div>
    </div>`).join('') || emptyRow('Belum ada aktivitas.');

  const dueSoon = loans.filter(l=>!l.returnDate).map(l=>({l, days:diffDays(TODAY,l.dueDate)})).sort((a,b)=>a.days-b.days).slice(0,6);
  document.getElementById('due-soon-list').innerHTML = dueSoon.map(({l,days})=>{
    const b = bookById(l.bookId), m = memberById(l.memberId);
    let tag, tagClass;
    if(days<0){tag=`Telat ${-days} hr`; tagClass='background:var(--danger-soft);color:var(--danger);';}
    else if(days===0){tag='Hari ini'; tagClass='background:var(--danger-soft);color:var(--danger);';}
    else if(days<=2){tag=`${days} hr lagi`; tagClass='background:var(--brass-soft);color:#8f6524;';}
    else{tag=`${days} hr lagi`; tagClass='background:var(--success-soft);color:var(--success);';}
    return `<div class="due-item">
      <div class="spine" style="background:${spineColor(b.id)};"></div>
      <div><div class="due-title">${b.title}</div><div class="due-sub">${m.name}</div></div>
      <span class="due-tag" style="${tagClass}">${tag}</span>
    </div>`;
  }).join('') || emptyRow('Tidak ada jatuh tempo mendatang.');

  const catCount = {};
  loans.forEach(l=>{ const b=bookById(l.bookId); catCount[b.category]=(catCount[b.category]||0)+1; });
  const catArr = Object.entries(catCount).sort((a,b)=>b[1]-a[1]);
  const catMax = catArr.length? catArr[0][1] : 1;
  document.getElementById('category-list').innerHTML = catArr.map(([cat,count])=>`
    <div style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;"><span style="font-weight:600;">${cat}</span><span class="mono" style="color:var(--muted);">${count}x</span></div>
      <div style="height:6px;background:var(--line-soft);border-radius:6px;overflow:hidden;"><div style="height:100%;width:${(count/catMax*100)}%;background:var(--primary);border-radius:6px;"></div></div>
    </div>`).join('') || emptyRow('Belum ada data.');

  document.getElementById('badge-overdue').textContent = overdue;
}
function emptyRow(msg){ return `<div style="padding:16px 0;text-align:center;font-size:12px;color:var(--muted);">${msg}</div>`; }

/* ============================= RENDER: CATALOG ============================= */
function populateCategorySelects(){
  ['cat-filter-kategori','books-filter-kategori'].forEach(id=>{
    const el = document.getElementById(id);
    if(el) el.innerHTML = '<option value="">Semua Kategori</option>' + CATEGORIES.map(c=>`<option>${c}</option>`).join('');
  });
}
function setCatalogView(v){
  catalogView = v;
  document.getElementById('seg-grid').classList.toggle('active', v==='grid');
  document.getElementById('seg-list').classList.toggle('active', v==='list');
  document.getElementById('catalog-grid').style.display = v==='grid' ? 'grid':'none';
  document.getElementById('catalog-list').style.display = v==='list' ? 'block':'none';
}
function filteredCatalogBooks(){
  const q = document.getElementById('cat-search').value.toLowerCase();
  const cat = document.getElementById('cat-filter-kategori').value;
  const status = document.getElementById('cat-filter-status').value;
  return books.filter(b=>{
    if(q && !(b.title.toLowerCase().includes(q) || b.author.toLowerCase().includes(q))) return false;
    if(cat && b.category!==cat) return false;
    if(status==='tersedia' && b.available<=0) return false;
    if(status==='dipinjam' && b.available>0) return false;
    return true;
  });
}
function renderCatalog(){
  const list = filteredCatalogBooks();
  document.getElementById('catalog-count').textContent = `Menampilkan ${list.length} dari ${books.length} judul koleksi`;
  document.getElementById('catalog-grid').innerHTML = list.map(b=>{
    const statusPill = b.available<=0 ? '<span class="pill out">Dipinjam</span>' : (b.available<=1 ? '<span class="pill low">Sisa '+b.available+'</span>' : '<span class="pill available">Tersedia</span>');
    return `<div class="book-card" onclick="showBookDetail(${b.id})">
      <div class="book-cover" style="background:${spineColor(b.id)};">
        <div class="spine-mark" style="background:rgba(0,0,0,.18);"></div>
        <div class="book-cover-title">${b.title}</div>
      </div>
      <div class="book-body">
        <div class="book-title">${b.title}</div>
        <div class="book-author">${b.author}</div>
        <div class="book-meta"><span class="call-no">${b.callno}</span>${statusPill}</div>
      </div>
    </div>`;
  }).join('') || `<div class="empty" style="grid-column:1/-1;"><h4>Tidak ditemukan</h4><p>Coba ubah kata kunci atau filter pencarian.</p></div>`;

  document.getElementById('catalog-list-body').innerHTML = list.map(b=>{
    const statusBadge = b.available<=0 ? '<span class="status-badge dipinjam">Dipinjam</span>' : '<span class="status-badge tersedia">Tersedia</span>';
    return `<tr onclick="showBookDetail(${b.id})" style="cursor:pointer;">
      <td><div class="cell-main">${b.title}</div><div class="cell-sub">${b.author}</div></td>
      <td>${b.category}</td><td class="mono">${b.callno}</td>
      <td class="mono">${b.available}/${b.stock}</td><td>${statusBadge}</td>
    </tr>`;
  }).join('');
}
function showBookDetail(id){
  const b = bookById(id);
  const activeL = activeLoansForBook(id);
  document.getElementById('detail-body').innerHTML = `
    <div style="display:flex;gap:16px;margin-bottom:16px;">
      <div style="width:70px;height:96px;border-radius:6px;background:${spineColor(b.id)};flex-shrink:0;"></div>
      <div>
        <div style="font-family:'Source Serif 4',serif;font-size:16px;font-weight:600;">${b.title}</div>
        <div style="font-size:12.5px;color:var(--muted);margin-top:2px;">${b.author}</div>
        <div style="margin-top:8px;">${b.available>0?'<span class="pill available">Tersedia</span>':'<span class="pill out">Dipinjam</span>'}</div>
      </div>
    </div>
    <div class="field-row">
      <div><div class="cell-sub">ISBN</div><div class="mono" style="font-size:12.5px;">${b.isbn}</div></div>
      <div><div class="cell-sub">No. Panggil</div><div class="mono" style="font-size:12.5px;">${b.callno}</div></div>
    </div>
    <div class="field-row" style="margin-top:12px;">
      <div><div class="cell-sub">Penerbit</div><div style="font-size:12.5px;">${b.publisher}, ${b.year}</div></div>
      <div><div class="cell-sub">Lokasi Rak</div><div style="font-size:12.5px;">${b.shelf}</div></div>
    </div>
    <div class="field-row" style="margin-top:12px;">
      <div><div class="cell-sub">Kategori</div><div style="font-size:12.5px;">${b.category}</div></div>
      <div><div class="cell-sub">Ketersediaan</div><div style="font-size:12.5px;">${b.available} dari ${b.stock} eksemplar</div></div>
    </div>
    ${activeL.length? `<div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--line-soft);"><div class="cell-sub" style="margin-bottom:8px;">Sedang dipinjam oleh</div>` + activeL.map(l=>`<div style="font-size:12.5px;margin-bottom:4px;">• ${memberById(l.memberId).name} — jatuh tempo ${fmtDate(l.dueDate)}</div>`).join('') + `</div>`:''}
  `;
  openModal('detail-modal');
}

/* ============================= RENDER: BOOKS ADMIN ============================= */
function filteredAdminBooks(){
  const q = document.getElementById('books-search').value.toLowerCase();
  const cat = document.getElementById('books-filter-kategori').value;
  return books.filter(b=>{
    if(q && !(b.title.toLowerCase().includes(q)||b.author.toLowerCase().includes(q)||b.isbn.toLowerCase().includes(q))) return false;
    if(cat && b.category!==cat) return false;
    return true;
  });
}
function renderBooksTable(){
  const list = filteredAdminBooks();
  document.getElementById('books-count').textContent = `${list.length} judul`;
  document.getElementById('books-table-body').innerHTML = list.map(b=>{
    const statusBadge = b.available<=0 ? '<span class="status-badge dipinjam">Dipinjam</span>' : (b.available<=1? '<span class="status-badge dipinjam">Stok Rendah</span>':'<span class="status-badge tersedia">Tersedia</span>');
    return `<tr>
      <td><div class="cell-main">${b.title}</div><div class="cell-sub">${b.author} · ${b.isbn}</div></td>
      <td>${b.category}</td>
      <td class="mono">${b.callno}</td>
      <td>${b.publisher}<div class="cell-sub">${b.year}</div></td>
      <td class="mono">${b.available}/${b.stock}</td>
      <td>${statusBadge}</td>
      <td><div class="row-actions">
        <button class="icon-mini" title="Detail" onclick="showBookDetail(${b.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg></button>
        <button class="icon-mini" title="Edit" onclick="openBookModal(${b.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>
        <button class="icon-mini danger" title="Hapus" onclick="deleteBook(${b.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg></button>
      </div></td>
    </tr>`;
  }).join('') || `<tr><td colspan="7">${emptyRow('Tidak ada buku ditemukan.')}</td></tr>`;
}
function openBookModal(id){
  document.getElementById('book-modal-title').textContent = id ? 'Edit Buku' : 'Tambah Buku';
  const f = id ? bookById(id) : {id:'',title:'',author:'',isbn:'',category:'Fiksi',callno:'',publisher:'',year:'',stock:'',shelf:''};
  document.getElementById('bk-id').value = f.id;
  document.getElementById('bk-title').value = f.title;
  document.getElementById('bk-author').value = f.author;
  document.getElementById('bk-isbn').value = f.isbn;
  document.getElementById('bk-category').value = f.category;
  document.getElementById('bk-callno').value = f.callno;
  document.getElementById('bk-publisher').value = f.publisher;
  document.getElementById('bk-year').value = f.year;
  document.getElementById('bk-stock').value = f.stock;
  document.getElementById('bk-shelf').value = f.shelf;
  openModal('book-modal');
}
function saveBook(){
  const id = document.getElementById('bk-id').value;
  const title = document.getElementById('bk-title').value.trim();
  if(!title){ toast('Judul buku wajib diisi.', true); return; }
  const stock = parseInt(document.getElementById('bk-stock').value)||0;
  const data = {
    title, author: document.getElementById('bk-author').value.trim(),
    isbn: document.getElementById('bk-isbn').value.trim(),
    category: document.getElementById('bk-category').value,
    callno: document.getElementById('bk-callno').value.trim(),
    publisher: document.getElementById('bk-publisher').value.trim(),
    year: parseInt(document.getElementById('bk-year').value)||'',
    stock, shelf: document.getElementById('bk-shelf').value.trim(),
  };
  if(id){
    const b = bookById(parseInt(id));
    const borrowed = b.stock - b.available;
    Object.assign(b, data);
    b.available = Math.max(0, data.stock - borrowed);
    toast('Buku berhasil diperbarui.');
  } else {
    books.push({id: nextBookId++, available: stock, ...data});
    toast('Buku baru berhasil ditambahkan.');
  }
  closeModal('book-modal');
  renderAll();
}
function deleteBook(id){
  if(!confirm('Hapus buku ini dari koleksi?')) return;
  books = books.filter(b=>b.id!==id);
  toast('Buku dihapus dari koleksi.');
  renderAll();
}

/* ============================= RENDER: CIRCULATION ============================= */
function populateLoanForm(){
  document.getElementById('loan-member').innerHTML = members.filter(m=>m.status==='aktif').map(m=>`<option value="${m.id}">${m.name} (${m.no})</option>`).join('');
  document.getElementById('loan-book').innerHTML = books.filter(b=>b.available>0).map(b=>`<option value="${b.id}">${b.title} — sisa ${b.available}</option>`).join('') || '<option disabled>Tidak ada buku tersedia</option>';
  document.getElementById('loan-date').value = TODAY;
  document.getElementById('loan-due').value = daysFromNow(14);

  document.getElementById('res-member').innerHTML = members.filter(m=>m.status==='aktif').map(m=>`<option value="${m.id}">${m.name} (${m.no})</option>`).join('');
  document.getElementById('res-book').innerHTML = books.map(b=>`<option value="${b.id}">${b.title} ${b.available<=0?'(dipinjam)':'(tersedia)'}</option>`).join('');
}
function createLoan(){
  const memberId = parseInt(document.getElementById('loan-member').value);
  const bookId = parseInt(document.getElementById('loan-book').value);
  if(!memberId || !bookId){ toast('Pilih anggota dan buku terlebih dahulu.', true); return; }
  const b = bookById(bookId);
  if(b.available<=0){ toast('Stok buku ini sudah habis.', true); return; }
  loans.push({id:nextLoanId++, memberId, bookId, loanDate: document.getElementById('loan-date').value, dueDate: document.getElementById('loan-due').value, returnDate:null});
  b.available -= 1;
  toast(`Peminjaman "${b.title}" berhasil diproses.`);
  populateLoanForm();
  renderAll();
}
function returnLoan(id){
  const l = loans.find(x=>x.id===id);
  l.returnDate = TODAY;
  const b = bookById(l.bookId);
  b.available = Math.min(b.stock, b.available+1);
  const fine = calcFine(l);
  toast(fine>0 ? `Buku dikembalikan. Denda keterlambatan: ${rupiah(fine)}` : 'Buku berhasil dikembalikan.');
  populateLoanForm();
  renderAll();
}
function extendLoan(id){
  const l = loans.find(x=>x.id===id);
  l.dueDate = daysFromNow(diffDays(TODAY,l.dueDate) + 7 > 0 ? diffDays(TODAY,l.dueDate)+7 : 7);
  toast('Masa pinjam diperpanjang 7 hari.');
  renderAll();
}
function setLoanFilter(status, el){
  loanFilter = status;
  document.querySelectorAll('#page-circulation .seg button').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  renderLoans();
}
function renderLoans(){
  const q = document.getElementById('loan-search').value.toLowerCase();
  let list = loans.slice().sort((a,b)=> b.id-a.id);
  if(loanFilter) list = list.filter(l=>loanStatus(l)===loanFilter);
  if(q) list = list.filter(l=>{
    const m=memberById(l.memberId), b=bookById(l.bookId);
    return m.name.toLowerCase().includes(q) || b.title.toLowerCase().includes(q);
  });
  document.getElementById('loans-table-body').innerHTML = list.map(l=>{
    const m = memberById(l.memberId), b = bookById(l.bookId), st = loanStatus(l);
    const badge = st==='aktif' ? '<span class="status-badge aktif">Aktif</span>' : st==='terlambat' ? '<span class="status-badge terlambat">Terlambat</span>' : '<span class="status-badge kembali">Selesai</span>';
    const actions = st==='kembali' ? `<span class="cell-sub">Kembali ${fmtDate(l.returnDate)}</span>` : `
      <div class="row-actions">
        <button class="icon-mini" title="Perpanjang" onclick="extendLoan(${l.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg></button>
        <button class="icon-mini" title="Kembalikan" onclick="returnLoan(${l.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></button>
      </div>`;
    return `<tr>
      <td><div class="cell-main">${m.name}</div><div class="cell-sub">${m.no}</div></td>
      <td><div class="cell-main">${b.title}</div><div class="cell-sub">${b.callno}</div></td>
      <td class="mono">${fmtDate(l.loanDate)}</td>
      <td class="mono">${fmtDate(l.dueDate)}</td>
      <td>${badge}</td>
      <td>${actions}</td>
    </tr>`;
  }).join('') || `<tr><td colspan="6">${emptyRow('Tidak ada data peminjaman.')}</td></tr>`;
}

/* ============================= RENDER: RESERVATIONS ============================= */
function createReservation(){
  const memberId = parseInt(document.getElementById('res-member').value);
  const bookId = parseInt(document.getElementById('res-book').value);
  if(!memberId||!bookId){ toast('Pilih anggota dan buku.', true); return; }
  reservations.push({id:nextResId++, memberId, bookId, date:TODAY, status:'menunggu'});
  toast('Reservasi berhasil ditambahkan ke antrean.');
  renderAll();
}
function cancelReservation(id){
  reservations = reservations.filter(r=>r.id!==id);
  toast('Reservasi dibatalkan.');
  renderAll();
}
function fulfillReservation(id){
  const r = reservations.find(x=>x.id===id);
  r.status='selesai';
  toast('Reservasi ditandai selesai.');
  renderAll();
}
function renderReservations(){
  const list = reservations.slice().sort((a,b)=>b.id-a.id);
  document.getElementById('reservations-table-body').innerHTML = list.map((r,i)=>{
    const m=memberById(r.memberId), b=bookById(r.bookId);
    const badge = r.status==='menunggu' ? '<span class="status-badge dipinjam">Menunggu</span>' : r.status==='siap' ? '<span class="status-badge tersedia">Siap Diambil</span>' : '<span class="status-badge kembali">Selesai</span>';
    const actions = r.status!=='selesai' ? `<div class="row-actions">
        <button class="icon-mini" title="Tandai selesai" onclick="fulfillReservation(${r.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></button>
        <button class="icon-mini danger" title="Batalkan" onclick="cancelReservation(${r.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
      </div>` : '';
    return `<tr>
      <td class="mono">#${i+1}</td>
      <td><div class="cell-main">${b.title}</div><div class="cell-sub">${b.callno}</div></td>
      <td>${m.name}</td>
      <td class="mono">${fmtDate(r.date)}</td>
      <td>${badge}</td>
      <td>${actions}</td>
    </tr>`;
  }).join('') || `<tr><td colspan="6">${emptyRow('Belum ada reservasi.')}</td></tr>`;
}

/* ============================= RENDER: FINES ============================= */
function renderFines(){
  const q = (document.getElementById('fines-search').value||'').toLowerCase();
  const filter = document.getElementById('fines-filter').value;
  let rows = loans.map(l=>({l, fine: calcFine(l)})).filter(x=>x.fine>0);
  if(q) rows = rows.filter(x=> memberById(x.l.memberId).name.toLowerCase().includes(q));
  if(filter==='belum') rows = rows.filter(x=>!x.l.finePaid);
  if(filter==='lunas') rows = rows.filter(x=>x.l.finePaid);

  const totalUnpaid = loans.reduce((s,l)=> s + (!l.finePaid ? calcFine(l):0), 0);
  const totalPaid = loans.reduce((s,l)=> s + (l.finePaid ? calcFine(l):0), 0);
  const countUnpaid = loans.filter(l=>!l.finePaid && calcFine(l)>0).length;
  document.getElementById('fines-stats').innerHTML = [
    {label:'Denda Belum Dibayar', num:rupiah(totalUnpaid), color:'var(--danger)', bg:'var(--danger-soft)'},
    {label:'Denda Terkumpul', num:rupiah(totalPaid), color:'var(--success)', bg:'var(--success-soft)'},
    {label:'Anggota Menunggak', num:countUnpaid, color:'#8f6524', bg:'var(--brass-soft)'},
  ].map(s=>`<div class="card stat-card"><div class="stat-icon" style="background:${s.bg};color:${s.color};"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v10"/></svg></div><div class="stat-num">${s.num}</div><div class="stat-label">${s.label}</div></div>`).join('');

  document.getElementById('fines-table-body').innerHTML = rows.map(({l,fine})=>{
    const m=memberById(l.memberId), b=bookById(l.bookId);
    const late = diffDays(l.dueDate, l.returnDate||TODAY);
    const badge = l.finePaid ? '<span class="status-badge kembali">Lunas</span>' : '<span class="status-badge terlambat">Belum Dibayar</span>';
    const action = l.finePaid ? '' : `<button class="btn btn-ghost" style="padding:6px 12px;font-size:12px;" onclick="payFine(${l.id})">Tandai Lunas</button>`;
    return `<tr>
      <td><div class="cell-main">${m.name}</div><div class="cell-sub">${m.no}</div></td>
      <td>${b.title}</td>
      <td class="mono">${late} hari</td>
      <td class="mono" style="font-weight:600;">${rupiah(fine)}</td>
      <td>${badge}</td>
      <td>${action}</td>
    </tr>`;
  }).join('') || `<tr><td colspan="6">${emptyRow('Tidak ada catatan denda.')}</td></tr>`;
}
function payFine(loanId){
  const l = loans.find(x=>x.id===loanId);
  l.finePaid = true;
  toast('Pembayaran denda dicatat. Terima kasih!');
  renderAll();
}

/* ============================= RENDER: MEMBERS ============================= */
function renderMembers(){
  const q = document.getElementById('members-search').value.toLowerCase();
  const status = document.getElementById('members-filter-status').value;
  let list = members.filter(m=>{
    if(q && !(m.name.toLowerCase().includes(q)||m.email.toLowerCase().includes(q)||m.no.toLowerCase().includes(q))) return false;
    if(status && m.status!==status) return false;
    return true;
  });
  document.getElementById('members-count').textContent = `${list.length} anggota`;
  document.getElementById('members-table-body').innerHTML = list.map(m=>{
    const initials = m.name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
    const borrowed = activeLoanCountForMember(m.id);
    const fine = memberFine(m.id);
    const statusBadge = m.status==='aktif' ? '<span class="status-badge aktif">Aktif</span>' : '<span class="status-badge nonaktif">Nonaktif</span>';
    return `<tr>
      <td><div style="display:flex;align-items:center;gap:10px;">
        <div class="avatar-sm" style="background:var(--primary-soft);color:var(--primary);">${initials}</div>
        <div><div class="cell-main">${m.name}</div><div class="cell-sub">${m.no}</div></div>
      </div></td>
      <td><div style="font-size:12.3px;">${m.email}</div><div class="cell-sub">${m.phone}</div></td>
      <td>${m.type}</td>
      <td class="mono">${borrowed}</td>
      <td class="mono" style="color:${fine>0?'var(--danger)':'inherit'};">${fine>0?rupiah(fine):'—'}</td>
      <td>${statusBadge}</td>
      <td><div class="row-actions">
        <button class="icon-mini" title="Edit" onclick="openMemberModal(${m.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>
        <button class="icon-mini danger" title="Hapus" onclick="deleteMember(${m.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg></button>
      </div></td>
    </tr>`;
  }).join('') || `<tr><td colspan="7">${emptyRow('Tidak ada anggota ditemukan.')}</td></tr>`;
}
function openMemberModal(id){
  document.getElementById('member-modal-title').textContent = id ? 'Edit Anggota' : 'Tambah Anggota';
  const f = id ? memberById(id) : {id:'',name:'',email:'',phone:'',type:'Umum',status:'aktif'};
  document.getElementById('mb-id').value = f.id;
  document.getElementById('mb-name').value = f.name;
  document.getElementById('mb-email').value = f.email;
  document.getElementById('mb-phone').value = f.phone;
  document.getElementById('mb-type').value = f.type;
  document.getElementById('mb-status').value = f.status;
  openModal('member-modal');
}
function saveMember(){
  const id = document.getElementById('mb-id').value;
  const name = document.getElementById('mb-name').value.trim();
  if(!name){ toast('Nama anggota wajib diisi.', true); return; }
  const data = {
    name, email: document.getElementById('mb-email').value.trim(),
    phone: document.getElementById('mb-phone').value.trim(),
    type: document.getElementById('mb-type').value,
    status: document.getElementById('mb-status').value,
  };
  if(id){
    Object.assign(memberById(parseInt(id)), data);
    toast('Data anggota diperbarui.');
  } else {
    const no = 'AGT-' + String(nextMemberId).padStart(4,'0');
    members.push({id: nextMemberId++, no, ...data});
    toast('Anggota baru berhasil ditambahkan.');
  }
  closeModal('member-modal');
  renderAll();
}
function deleteMember(id){
  if(!confirm('Hapus anggota ini?')) return;
  members = members.filter(m=>m.id!==id);
  toast('Anggota dihapus.');
  renderAll();
}

/* ============================= RENDER: CALENDAR ============================= */
function renderCalendar(){
  const now = new Date();
  const monthNames=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  document.getElementById('cal-month-label').textContent = monthNames[now.getMonth()]+' '+now.getFullYear();
  document.getElementById('cal-dow-row').innerHTML = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'].map(d=>`<div class="cal-dow">${d}</div>`).join('');
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).getDay();
  const daysInMonth = new Date(now.getFullYear(), now.getMonth()+1, 0).getDate();
  const eventDates = new Set(events.map(e=>new Date(e.date).getDate()+'-'+new Date(e.date).getMonth()));
  let cells='';
  for(let i=0;i<firstDay;i++) cells += `<div class="cal-day muted">${new Date(now.getFullYear(),now.getMonth(),0).getDate()-firstDay+i+1}</div>`;
  for(let d=1; d<=daysInMonth; d++){
    const isToday = d===now.getDate();
    const hasEvent = eventDates.has(d+'-'+now.getMonth());
    cells += `<div class="cal-day ${isToday?'today':''} ${hasEvent?'event':''}">${d}</div>`;
  }
  document.getElementById('cal-grid').innerHTML = cells;

  document.getElementById('events-list').innerHTML = events.slice().sort((a,b)=>new Date(a.date)-new Date(b.date)).map(e=>{
    const d = new Date(e.date);
    const mon = d.toLocaleDateString('id-ID',{month:'short'});
    return `<div class="event-item">
      <div class="event-date"><b>${d.getDate()}</b><span>${mon}</span></div>
      <div><div style="font-size:12.8px;font-weight:600;">${e.title}</div><div class="cell-sub">${e.type}</div></div>
    </div>`;
  }).join('') || emptyRow('Belum ada kegiatan terjadwal.');
}

/* ============================= RENDER: REPORTS ============================= */
function renderReports(){
  const totalLoansMonth = loans.length;
  const returnedOnTime = loans.filter(l=>l.returnDate && diffDays(l.dueDate,l.returnDate)<=0).length;
  const totalFines = loans.reduce((s,l)=>s+calcFine(l),0);
  const newMembers = members.length;
  document.getElementById('report-stats').innerHTML = [
    {label:'Total Transaksi', num:totalLoansMonth, icon:'📊'},
    {label:'Dikembalikan Tepat Waktu', num: (loans.filter(l=>l.returnDate).length? Math.round(returnedOnTime/loans.filter(l=>l.returnDate).length*100):0)+'%', icon:'✅'},
    {label:'Total Denda Tercatat', num: rupiah(totalFines), icon:'💳'},
    {label:'Anggota Terdaftar', num: newMembers, icon:'👤'},
  ].map(s=>`<div class="card stat-card"><div style="font-size:22px;">${s.icon}</div><div class="stat-num">${s.num}</div><div class="stat-label">${s.label}</div></div>`).join('');

  const freq = {};
  loans.forEach(l=> freq[l.bookId]=(freq[l.bookId]||0)+1);
  const top = Object.entries(freq).sort((a,b)=>b[1]-a[1]).slice(0,6);
  const maxF = top.length? top[0][1]:1;
  document.getElementById('top-books-list').innerHTML = top.map(([bid,count],i)=>{
    const b = bookById(parseInt(bid));
    return `<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
      <div class="mono" style="width:20px;color:var(--muted-soft);font-size:12px;">#${i+1}</div>
      <div class="spine" style="background:${spineColor(b.id)};height:30px;"></div>
      <div style="flex:1;">
        <div style="font-size:12.6px;font-weight:600;">${b.title}</div>
        <div style="height:5px;background:var(--line-soft);border-radius:5px;margin-top:5px;overflow:hidden;"><div style="height:100%;width:${(count/maxF*100)}%;background:var(--brass);border-radius:5px;"></div></div>
      </div>
      <div class="mono" style="font-size:11.5px;color:var(--muted);">${count}x</div>
    </div>`;
  }).join('') || emptyRow('Belum ada data peminjaman.');
}

/* ============================= RENDER: SETTINGS ============================= */
function setSettingsTab(tab, el){
  settingsTab = tab;
  document.querySelectorAll('#settings-nav button').forEach(b=>b.classList.remove('active'));
  el.classList.add('active');
  renderSettings();
}
function renderSettings(){
  const body = document.getElementById('settings-body');
  if(settingsTab==='general'){
    body.innerHTML = `
      <h3 style="margin-top:0;font-size:15px;">Informasi Perpustakaan</h3>
      <div class="field"><label>Nama Perpustakaan</label><input type="text" value="Perpustakaan Umum Lestari Pustaka"></div>
      <div class="field-row">
        <div class="field"><label>Kode Institusi</label><input type="text" value="LP-2024-001"></div>
        <div class="field"><label>Zona Waktu</label><select><option>WIB (UTC+7)</option><option>WITA (UTC+8)</option><option>WIT (UTC+9)</option></select></div>
      </div>
      <div class="field"><label>Alamat</label><textarea rows="2">Jl. Pendidikan No. 45, Surakarta, Jawa Tengah</textarea></div>
      <button class="btn btn-primary" onclick="toast('Pengaturan umum disimpan.')">Simpan Perubahan</button>`;
  } else if(settingsTab==='policy'){
    body.innerHTML = `
      <h3 style="margin-top:0;font-size:15px;">Kebijakan Peminjaman</h3>
      <div class="field-row">
        <div class="field"><label>Maks. Buku Dipinjam / Anggota</label><input type="number" value="3"></div>
        <div class="field"><label>Durasi Peminjaman (hari)</label><input type="number" value="14"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Denda per Hari Terlambat</label><input type="text" value="Rp2.000"></div>
        <div class="field"><label>Maks. Perpanjangan</label><input type="number" value="2"></div>
      </div>
      <div class="set-row"><div><div class="t">Blokir peminjaman jika ada denda tertunggak</div><div class="d">Anggota tidak dapat meminjam sebelum melunasi denda.</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
      <div class="set-row"><div><div class="t">Izinkan reservasi otomatis</div><div class="d">Anggota bisa mengantre buku yang sedang dipinjam.</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
      <button class="btn btn-primary" style="margin-top:14px;" onclick="toast('Kebijakan peminjaman disimpan.')">Simpan Perubahan</button>`;
  } else if(settingsTab==='notif'){
    body.innerHTML = `
      <h3 style="margin-top:0;font-size:15px;">Preferensi Notifikasi</h3>
      <div class="set-row"><div><div class="t">Pengingat jatuh tempo (H-2)</div><div class="d">Kirim email ke anggota sebelum tanggal jatuh tempo.</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
      <div class="set-row"><div><div class="t">Notifikasi keterlambatan</div><div class="d">Kirim pemberitahuan otomatis saat buku terlambat.</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
      <div class="set-row"><div><div class="t">Notifikasi buku tersedia (reservasi)</div><div class="d">Beri tahu anggota saat buku yang diantre tersedia.</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
      <div class="set-row"><div><div class="t">Ringkasan mingguan untuk pustakawan</div><div class="d">Laporan aktivitas dikirim tiap Senin pagi.</div></div><div class="toggle" onclick="this.classList.toggle('on')"></div></div>`;
  } else {
    body.innerHTML = `
      <h3 style="margin-top:0;font-size:15px;">Pengguna &amp; Peran</h3>
      <div class="table-wrap"><table>
        <thead><tr><th>Nama</th><th>Peran</th><th>Email</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td class="cell-main">Ayu Ratnasari</td><td>Administrator</td><td>ayu.r@perpustakaan.id</td><td><span class="status-badge aktif">Aktif</span></td></tr>
          <tr><td class="cell-main">Budi Santoso</td><td>Pustakawan</td><td>budi.s@perpustakaan.id</td><td><span class="status-badge aktif">Aktif</span></td></tr>
          <tr><td class="cell-main">Rina Wijaya</td><td>Staf Sirkulasi</td><td>rina.w@perpustakaan.id</td><td><span class="status-badge nonaktif">Nonaktif</span></td></tr>
        </tbody>
      </table></div>
      <button class="btn btn-ghost" style="margin-top:14px;" onclick="toast('Fitur undang pengguna (contoh).')">+ Undang Pengguna</button>`;
  }
}

/* ============================= MODALS / TOAST ============================= */
function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
function toast(msg, isError){
  const wrap = document.getElementById('toast-wrap');
  const t = document.createElement('div');
  t.className='toast';
  t.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="${isError?'#F3A996':'#8ED9A6'}" stroke-width="2">${isError?'<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>':'<path d="M20 6L9 17l-5-5"/>'}</svg><span>${msg}</span>`;
  wrap.appendChild(t);
  setTimeout(()=>{ t.remove(); }, 3500);
}
document.addEventListener('DOMContentLoaded', () => {

    if (document.getElementById('page-dashboard')) {
        renderDashboard();
    }

    if (document.getElementById('catalog-grid')) {
        renderCatalog();
    }

    if (document.getElementById('books-table-body')) {
        renderBooksTable();
    }

    if (document.getElementById('loans-table-body')) {
        renderLoans();
    }

    if (document.getElementById('reservations-table-body')) {
        renderReservations();
    }

    if (document.getElementById('fines-table-body')) {
        renderFines();
    }

    if (document.getElementById('members-table-body')) {
        renderMembers();
    }

    if (document.getElementById('cal-grid')) {
        renderCalendar();
    }

    if (document.getElementById('report-stats')) {
        renderReports();
    }

    if (document.getElementById('settings-body')) {
        renderSettings();
    }

});