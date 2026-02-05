/* assets/script.js
   AkuntansiKu - Frontend Logic (LocalStorage JSON)
   - Login demo (admin/admin123)
   - CRUD Pelanggan
   - CRUD Transaksi
   - Dashboard + Jurnal (double-entry view sederhana)
   - Laporan filter + rekap + piutang
   - Download PDF laporan (html2pdf) dari #laporan-print
*/

/* =========================
   HELPERS
========================= */
const LS_KEYS = {
  USERS: 'ak_users_v1',
  CUSTOMERS: 'ak_customers_v1',
  TRANSACTIONS: 'ak_transactions_v1',
  AUTH: 'ak_auth_v1',
};

function rupiah(n) {
  n = Number(n || 0);
  return 'Rp ' + n.toLocaleString('id-ID');
}

function pad2(n){ return String(n).padStart(2,'0'); }

function todayISO(){
  const d = new Date();
  return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
}

function uid(){
  return Math.random().toString(16).slice(2) + Date.now().toString(16);
}

function readLS(key, fallback){
  try{
    const raw = localStorage.getItem(key);
    if(!raw) return fallback;
    return JSON.parse(raw);
  }catch(e){
    return fallback;
  }
}

function writeLS(key, val){
  localStorage.setItem(key, JSON.stringify(val));
}

function setMsg(elId, msg, isErr=false){
  const el = document.getElementById(elId);
  if(!el) return;
  el.textContent = msg || '';
  el.className = 'text-xs ' + (isErr ? 'text-rose-600' : 'text-slate-500');
}

function escHtml(s){
  return String(s ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}

function getActiveUser(){
  return readLS(LS_KEYS.AUTH, null);
}

function setActiveUser(user){
  if(!user) localStorage.removeItem(LS_KEYS.AUTH);
  else writeLS(LS_KEYS.AUTH, user);
}

/* =========================
   SEED DATA
========================= */
function seed(){
  // users
  let users = readLS(LS_KEYS.USERS, null);
  if(!users || !Array.isArray(users) || users.length === 0){
    users = [
      { id: 'u_admin', username: 'admin', password: 'admin123', role: 'Operator' }
    ];
    writeLS(LS_KEYS.USERS, users);
  }

  // customers
  let customers = readLS(LS_KEYS.CUSTOMERS, null);
  if(!customers || !Array.isArray(customers)){
    customers = [];
    writeLS(LS_KEYS.CUSTOMERS, customers);
  }

  // transactions
  let trx = readLS(LS_KEYS.TRANSACTIONS, null);
  if(!trx || !Array.isArray(trx)){
    trx = [];
    writeLS(LS_KEYS.TRANSACTIONS, trx);
  }
}

/* =========================
   PAGE NAV
========================= */
function showPage(pageId){
  document.querySelectorAll('.page-section').forEach(s=>s.classList.add('hidden'));
  const el = document.getElementById(pageId);
  if(el) el.classList.remove('hidden');

  const title = document.getElementById('page-title');
  if(title){
    const map = {
      dashboard: 'Dashboard',
      pelanggan: 'Pelanggan',
      transaksi: 'Penjualan',
      laporan: 'Laporan',
    };
    title.textContent = map[pageId] || 'Dashboard';
  }

  // refresh halaman tertentu
  if(pageId === 'dashboard') refreshDashboard();
  if(pageId === 'pelanggan') renderCustomers();
  if(pageId === 'transaksi') {
    hydrateTransaksiDefault();
    fillCustomerSelect();
    renderTransactionTable();
  }
  if(pageId === 'laporan') updateReport();
}

function showApp(){
  document.getElementById('login-page')?.classList.add('hidden');
  document.getElementById('app-container')?.classList.remove('hidden');
  showPage('dashboard');
}

function showLogin(){
  document.getElementById('app-container')?.classList.add('hidden');
  document.getElementById('login-page')?.classList.remove('hidden');
}

/* =========================
   AUTH (LocalStorage)
========================= */
function initLogin(){
  const loginForm = document.getElementById('loginForm');
  if(!loginForm) return;

  loginForm.addEventListener('submit', (e)=>{
    e.preventDefault();
    const username = document.getElementById('username')?.value?.trim() || '';
    const password = document.getElementById('password')?.value || '';
    const users = readLS(LS_KEYS.USERS, []);

    const user = users.find(u=>u.username === username && u.password === password);
    if(!user){
      alert('Login gagal. Pakai: admin / admin123');
      return;
    }

    setActiveUser({ id: user.id, username: user.username, role: user.role });
    showApp();
  });
}

function logout(){
  setActiveUser(null);
  showLogin();
}

/* =========================
   DATA ACCESSORS
========================= */
function getCustomers(){
  return readLS(LS_KEYS.CUSTOMERS, []);
}

function setCustomers(arr){
  writeLS(LS_KEYS.CUSTOMERS, arr);
}

function getTransactions(){
  return readLS(LS_KEYS.TRANSACTIONS, []);
}

function setTransactions(arr){
  writeLS(LS_KEYS.TRANSACTIONS, arr);
}

/* =========================
   CUSTOMERS
========================= */
function initCustomers(){
  const form = document.getElementById('pelangganForm');
  if(!form) return;

  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    const name = document.getElementById('pelanggan_nama')?.value?.trim() || '';
    const phone = document.getElementById('pelanggan_hp')?.value?.trim() || '';
    const address = document.getElementById('pelanggan_alamat')?.value?.trim() || '';

    if(!name){
      setMsg('pelangganFormMsg', 'Nama wajib diisi.', true);
      return;
    }

    const customers = getCustomers();
    customers.unshift({
      id: 'c_' + uid(),
      name,
      phone,
      address,
      created_at: new Date().toISOString()
    });
    setCustomers(customers);

    form.reset();
    setMsg('pelangganFormMsg', 'Pelanggan tersimpan ✅');
    renderCustomers();
    fillCustomerSelect();
    refreshDashboard();
  });
}

function calcPiutangByCustomerId(customerId){
  const trxs = getTransactions();
  return trxs
    .filter(t => t.method === 'Credit' && !t.is_paid && t.customer_id === customerId)
    .reduce((sum, t)=> sum + Number(t.total||0), 0);
}

function renderCustomers(){
  const tbody = document.getElementById('pelangganTbody');
  const empty = document.getElementById('pelangganEmpty');
  const totalEl = document.getElementById('pelangganTotal');
  if(!tbody) return;

  const q = (document.getElementById('pelangganSearch')?.value || '').trim().toLowerCase();
  let customers = getCustomers();

  if(q){
    customers = customers.filter(c =>
      (c.name||'').toLowerCase().includes(q) ||
      (c.phone||'').toLowerCase().includes(q)
    );
  }

  if(totalEl) totalEl.textContent = String(customers.length);

  if(customers.length === 0){
    tbody.innerHTML = '';
    empty?.classList.remove('hidden');
    return;
  }

  empty?.classList.add('hidden');

  tbody.innerHTML = customers.map((c, idx)=>{
    const piutang = calcPiutangByCustomerId(c.id);
    return `
      <tr>
        <td class="px-5 py-3">${idx+1}</td>
        <td class="px-5 py-3 font-semibold text-slate-900">${escHtml(c.name)}</td>
        <td class="px-5 py-3 text-slate-600">${escHtml(c.phone || '-')}</td>
        <td class="px-5 py-3 text-slate-600">${escHtml(c.address || '-')}</td>
        <td class="px-5 py-3 text-right font-semibold no-print">${rupiah(piutang)}</td>
        <td class="px-5 py-3 text-right no-print">
          <button onclick="deleteCustomer('${c.id}')" class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold">
            <i class="fa-solid fa-trash mr-1"></i>Hapus
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

function deleteCustomer(id){
  if(!confirm('Hapus pelanggan ini?')) return;
  let customers = getCustomers();
  customers = customers.filter(c=>c.id !== id);
  setCustomers(customers);

  // tidak hapus transaksi, tapi transaksi dengan customer_id yang hilang tetap pakai nama manual (kalau ada)
  renderCustomers();
  fillCustomerSelect();
  refreshDashboard();
}

/* =========================
   TRANSACTIONS
========================= */
function initTransactions(){
  const form = document.getElementById('transaksiForm');
  if(!form) return;

  // default tanggal hari ini
  hydrateTransaksiDefault();

  // auto: kalau Cash => lunas true & disable
  const metodeEl = document.getElementById('trx_metode');
  const lunasEl = document.getElementById('trx_lunas');
  const handleMetode = ()=>{
    const metode = metodeEl?.value || 'Cash';
    if(!lunasEl) return;
    if(metode === 'Cash'){
      lunasEl.checked = true;
      lunasEl.disabled = true;
    } else {
      lunasEl.disabled = false;
    }
  };
  metodeEl?.addEventListener('change', handleMetode);
  handleMetode();

  // update total & laba view
  const fields = ['trx_modal','trx_harga','trx_qty'];
  fields.forEach(id=>{
    document.getElementById(id)?.addEventListener('input', updateTransaksiView);
  });

  form.addEventListener('submit', (e)=>{
    e.preventDefault();

    const trx_date = document.getElementById('trx_tanggal')?.value || '';
    const customer_id = document.getElementById('trx_pelanggan_id')?.value || '';
    const customer_name_manual = document.getElementById('trx_nama_pelanggan')?.value?.trim() || '';
    const item_name = document.getElementById('trx_barang')?.value?.trim() || '';

    const cost = Number(document.getElementById('trx_modal')?.value || 0);
    const price = Number(document.getElementById('trx_harga')?.value || 0);
    const qty = Math.max(1, Number(document.getElementById('trx_qty')?.value || 1));
    const method = document.getElementById('trx_metode')?.value || 'Cash';
    const is_paid = method === 'Cash' ? true : !!document.getElementById('trx_lunas')?.checked;

    if(!trx_date) return setMsg('trxMsg','Tanggal wajib.', true);
    if(!item_name) return setMsg('trxMsg','Nama barang wajib.', true);

    const total = Math.max(0, price * qty);
    const profit = Math.max(0, (price - cost) * qty);

    const transactions = getTransactions();
    transactions.unshift({
      id: 't_' + uid(),
      trx_date,
      customer_id: customer_id || null,
      customer_name_manual: customer_id ? '' : customer_name_manual,
      item_name,
      cost: Math.max(0, Math.round(cost)),
      price: Math.max(0, Math.round(price)),
      qty: Math.round(qty),
      method,
      is_paid,
      total: Math.round(total),
      profit: Math.round(profit),
      created_at: new Date().toISOString()
    });
    setTransactions(transactions);

    setMsg('trxMsg', 'Transaksi tersimpan ✅');
    form.reset();
    hydrateTransaksiDefault();
    handleMetode();
    updateTransaksiView();
    renderTransactionTable();
    refreshDashboard();
    updateReport();
  });
}

function hydrateTransaksiDefault(){
  const tgl = document.getElementById('trx_tanggal');
  if(tgl && !tgl.value) tgl.value = todayISO();
  updateTransaksiView();
}

function updateTransaksiView(){
  const cost = Number(document.getElementById('trx_modal')?.value || 0);
  const price = Number(document.getElementById('trx_harga')?.value || 0);
  const qty = Math.max(1, Number(document.getElementById('trx_qty')?.value || 1));

  const total = Math.max(0, price * qty);
  const profit = Math.max(0, (price - cost) * qty);

  document.getElementById('trx_total_view').textContent = rupiah(total);
  document.getElementById('trx_laba_view').textContent = rupiah(profit);
}

function resetTransaksiForm(){
  const form = document.getElementById('transaksiForm');
  if(!form) return;
  form.reset();
  hydrateTransaksiDefault();
  // trigger cash default
  const metodeEl = document.getElementById('trx_metode');
  if(metodeEl) metodeEl.value = 'Cash';
  const lunasEl = document.getElementById('trx_lunas');
  if(lunasEl){
    lunasEl.checked = true;
    lunasEl.disabled = true;
  }
  updateTransaksiView();
  setMsg('trxMsg','Form direset.');
}

function getCustomerNameForTrx(trx){
  if(trx.customer_id){
    const customers = getCustomers();
    const c = customers.find(x=>x.id === trx.customer_id);
    return c?.name || '(Pelanggan dihapus)';
  }
  return trx.customer_name_manual || '-';
}

function renderTransactionTable(){
  const tbody = document.getElementById('trxTbody');
  const empty = document.getElementById('trxEmpty');
  const countEl = document.getElementById('trxTotalCount');
  if(!tbody) return;

  const q = (document.getElementById('trxSearch')?.value || '').trim().toLowerCase();
  let trxs = getTransactions();

  if(q){
    trxs = trxs.filter(t=>{
      const custName = getCustomerNameForTrx(t).toLowerCase();
      return (t.item_name||'').toLowerCase().includes(q) || custName.includes(q);
    });
  }

  if(countEl) countEl.textContent = String(trxs.length);

  if(trxs.length === 0){
    tbody.innerHTML = '';
    empty?.classList.remove('hidden');
    return;
  }
  empty?.classList.add('hidden');

  tbody.innerHTML = trxs.map(t=>{
    const cust = escHtml(getCustomerNameForTrx(t));
    const status = t.method === 'Cash'
      ? '<span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">Lunas</span>'
      : (t.is_paid
          ? '<span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">Lunas</span>'
          : '<span class="px-2 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold">Belum</span>'
        );

    return `
      <tr>
        <td class="px-5 py-3 whitespace-nowrap">${escHtml(t.trx_date)}</td>
        <td class="px-5 py-3">${cust}</td>
        <td class="px-5 py-3 font-semibold text-slate-900">${escHtml(t.item_name)}</td>
        <td class="px-5 py-3 text-right font-semibold">${rupiah(t.total)}</td>
        <td class="px-5 py-3 text-right font-semibold">${rupiah(t.profit)}</td>
        <td class="px-5 py-3">${escHtml(t.method)}</td>
        <td class="px-5 py-3">${status}</td>
        <td class="px-5 py-3 text-right no-print">
          <button onclick="openStruk('${t.id}')"
            class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold">
            <i class="fa-solid fa-receipt mr-1"></i>Struk
          </button>
          <button onclick="deleteTransaction('${t.id}')"
            class="ml-2 px-3 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold">
            <i class="fa-solid fa-trash mr-1"></i>Hapus
          </button>
        </td>
      </tr>
    `;
  }).join('');

  // update dashboard & jurnal ringkas
  refreshDashboard();
}

function deleteTransaction(id){
  if(!confirm('Hapus transaksi ini?')) return;
  let trxs = getTransactions();
  trxs = trxs.filter(t=>t.id !== id);
  setTransactions(trxs);

  renderTransactionTable();
  refreshDashboard();
  updateReport();
}

/* =========================
   STRUK MODAL
========================= */
function openStruk(trxId){
  const trxs = getTransactions();
  const t = trxs.find(x=>x.id === trxId);
  if(!t) return;

  const cust = getCustomerNameForTrx(t);
  const status = t.method === 'Cash' ? 'Lunas' : (t.is_paid ? 'Lunas' : 'Belum Lunas');

  document.getElementById('struk_tanggal').textContent = t.trx_date;
  document.getElementById('struk_pelanggan').textContent = cust;
  document.getElementById('struk_barang').textContent = t.item_name;
  document.getElementById('struk_qty').textContent = String(t.qty);
  document.getElementById('struk_harga').textContent = rupiah(t.price);
  document.getElementById('struk_total').textContent = rupiah(t.total);
  document.getElementById('struk_metode').textContent = t.method;
  document.getElementById('struk_status').textContent = status;

  document.getElementById('struk-overlay')?.classList.remove('hidden');
}

function closeStruk(){
  document.getElementById('struk-overlay')?.classList.add('hidden');
}

/* =========================
   DASHBOARD + JURNAL
========================= */
function refreshDashboard(){
  const trxs = getTransactions();
  const customers = getCustomers();

  const omset = trxs.reduce((sum,t)=> sum + Number(t.total||0), 0);
  const profit = trxs.reduce((sum,t)=> sum + Number(t.profit||0), 0);

  document.getElementById('dash-omset').textContent = rupiah(omset);
  document.getElementById('dash-profit').textContent = rupiah(profit);
  document.getElementById('dash-transaksi').textContent = String(trxs.length);
  document.getElementById('dash-pelanggan').textContent = String(customers.length);

  renderJurnal();
}

function renderJurnal(){
  const tbody = document.getElementById('jurnal-body');
  if(!tbody) return;

  const trxs = getTransactions().slice(0,5);

  // Jurnal sederhana:
  // Cash:
  //   Debit Kas = Total
  //   Kredit Penjualan = Total
  // Credit:
  //   Debit Piutang = Total
  //   Kredit Penjualan = Total
  tbody.innerHTML = trxs.map(t=>{
    const total = Number(t.total||0);
    const akunDebit = t.method === 'Cash' ? 'Kas' : 'Piutang Usaha';
    const akunKredit = 'Pendapatan Penjualan';
    const ket = `${akunDebit} / ${akunKredit} — ${t.item_name}`;

    return `
      <tr>
        <td class="px-5 py-3 whitespace-nowrap">${escHtml(t.trx_date)}</td>
        <td class="px-5 py-3 text-slate-700">${escHtml(ket)}</td>
        <td class="px-5 py-3 text-right font-semibold">${rupiah(total)}</td>
        <td class="px-5 py-3 text-right font-semibold">${rupiah(total)}</td>
      </tr>
    `;
  }).join('');
}

/* =========================
   LAPORAN
========================= */
function resetLaporan(){
  const from = document.getElementById('lap_from');
  const to = document.getElementById('lap_to');
  const metode = document.getElementById('lap_metode');
  if(from) from.value = '';
  if(to) to.value = '';
  if(metode) metode.value = '';
  updateReport();
}

function updateReport(){
  const from = document.getElementById('lap_from')?.value || '';
  const to = document.getElementById('lap_to')?.value || '';
  const metode = document.getElementById('lap_metode')?.value || '';

  let trxs = getTransactions();

  // filter date inclusive
  if(from){
    trxs = trxs.filter(t => t.trx_date >= from);
  }
  if(to){
    trxs = trxs.filter(t => t.trx_date <= to);
  }
  if(metode){
    trxs = trxs.filter(t => t.method === metode);
  }

  // rekap
  const omset = trxs.reduce((sum,t)=> sum + Number(t.total||0), 0);
  const laba = trxs.reduce((sum,t)=> sum + Number(t.profit||0), 0);
  const count = trxs.length;
  const piutang = trxs
    .filter(t => t.method === 'Credit' && !t.is_paid)
    .reduce((sum,t)=> sum + Number(t.total||0), 0);

  document.getElementById('lap_omset').textContent = rupiah(omset);
  document.getElementById('lap_laba').textContent = rupiah(laba);
  document.getElementById('lap_count').textContent = String(count);
  document.getElementById('lap_piutang').textContent = rupiah(piutang);

  // table
  const tbody = document.getElementById('lap_tbody');
  const empty = document.getElementById('lap_empty');
  if(!tbody) return;

  if(trxs.length === 0){
    tbody.innerHTML = '';
    empty?.classList.remove('hidden');
  } else {
    empty?.classList.add('hidden');
    tbody.innerHTML = trxs.map(t=>{
      const cust = escHtml(getCustomerNameForTrx(t));
      const statusText = t.method === 'Cash' ? 'Lunas' : (t.is_paid ? 'Lunas' : 'Belum');
      return `
        <tr>
          <td class="px-5 py-3 whitespace-nowrap">${escHtml(t.trx_date)}</td>
          <td class="px-5 py-3">${cust}</td>
          <td class="px-5 py-3 font-semibold text-slate-900">${escHtml(t.item_name)}</td>
          <td class="px-5 py-3 text-right">${escHtml(t.qty)}</td>
          <td class="px-5 py-3 text-right font-semibold">${rupiah(t.total)}</td>
          <td class="px-5 py-3 text-right font-semibold">${rupiah(t.profit)}</td>
          <td class="px-5 py-3">${escHtml(t.method)}</td>
          <td class="px-5 py-3">${escHtml(statusText)}</td>
        </tr>
      `;
    }).join('');
  }

  // === SYNC PRINT AREA (biar PDF keisi) ===
  const periodeEl = document.getElementById('lap_print_periode');
  if(periodeEl){
    const _from = from || '-';
    const _to = to || '-';
    periodeEl.textContent = `Periode: ${_from} s/d ${_to}`;
  }

  const genEl = document.getElementById('lap_print_generated');
  if(genEl){
    genEl.textContent = 'Generated: ' + new Date().toLocaleString('id-ID');
  }

  document.getElementById('lap_print_omset').textContent = rupiah(omset);
  document.getElementById('lap_print_laba').textContent = rupiah(laba);
  document.getElementById('lap_print_count').textContent = String(count);
  document.getElementById('lap_print_piutang').textContent = rupiah(piutang);

  const printBody = document.getElementById('lap_print_tbody');
  if(printBody) printBody.innerHTML = tbody.innerHTML;
}

/* =========================
   DOWNLOAD PDF (html2pdf)
   (index.html manggil ini)
========================= */
async function downloadLaporanPDF(){
  // refresh dulu biar data terbaru masuk print area
  try { updateReport(); } catch(e){}

  const printEl = document.getElementById('laporan-print');
  if(!printEl){
    alert('Area print laporan tidak ditemukan (#laporan-print).');
    return;
  }

  const from = document.getElementById('lap_from')?.value || '-';
  const to   = document.getElementById('lap_to')?.value || '-';

  // tampilkan sementara (html2pdf butuh visible layout)
  const wasHidden = printEl.classList.contains('hidden');
  if (wasHidden) printEl.classList.remove('hidden');

  const safe = (s)=> String(s).replace(/[^\w\-]+/g,'_');
  const filename = `Laporan_AkuntansiKu_${safe(from)}_${safe(to)}.pdf`;

  const opt = {
    margin: 8,
    filename,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true, letterRendering: true },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
  };

  try{
    await html2pdf().set(opt).from(printEl).save();
  } finally {
    if (wasHidden) printEl.classList.add('hidden');
  }
}

/* =========================
   SELECT PELANGGAN
========================= */
function fillCustomerSelect(){
  const sel = document.getElementById('trx_pelanggan_id');
  if(!sel) return;

  const customers = getCustomers();
  const current = sel.value;

  sel.innerHTML = `<option value="">— Pilih pelanggan (opsional) —</option>` + customers.map(c=>{
    return `<option value="${escHtml(c.id)}">${escHtml(c.name)}${c.phone ? ' — '+escHtml(c.phone) : ''}</option>`;
  }).join('');

  // restore value if still exists
  if(current && customers.some(c=>c.id === current)){
    sel.value = current;
  }
}

/* =========================
   INIT
========================= */
(function boot(){
  seed();
  initLogin();
  initCustomers();
  initTransactions();

  // set default tanggal transaksi
  document.getElementById('trx_tanggal') && (document.getElementById('trx_tanggal').value = todayISO());

  // auth check
  const user = getActiveUser();
  if(user) showApp();
  else showLogin();

  // initial render
  fillCustomerSelect();
  renderCustomers();
  renderTransactionTable();
  refreshDashboard();
  updateReport();
})();
