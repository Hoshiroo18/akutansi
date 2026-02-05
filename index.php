<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AkuntansiKu — Sistem Akuntansi Penjualan</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet"/>

  <!-- Optional: CSS kamu -->
  <link rel="stylesheet" href="assets/style.css" />

  <style>
    *{ font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    .glass { backdrop-filter: blur(14px); background: rgba(255,255,255,.72); }
    .glass-dark { backdrop-filter: blur(14px); background: rgba(15,23,42,.55); }
    .fade-in { animation: fadeIn .25s ease-out both; }
    @keyframes fadeIn { from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:translateY(0)} }

    ::-webkit-scrollbar{ width: 10px; height: 10px; }
    ::-webkit-scrollbar-thumb{ background: rgba(100,116,139,.35); border-radius: 999px; }
    ::-webkit-scrollbar-track{ background: rgba(2,6,23,.04); }

    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
      #app-container { overflow: visible !important; height: auto !important; }
    }
  </style>
</head>

<body class="text-slate-800 bg-slate-50">

  <!-- =========================
       LOGIN PAGE
  ========================== -->
  <div id="login-page" class="min-h-screen flex items-center justify-center px-4 py-10 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900">
    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

      <!-- Left Promo -->
      <div class="hidden lg:flex flex-col justify-between rounded-3xl p-10 text-white shadow-2xl border border-white/10 glass-dark">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
              <i class="fa-solid fa-chart-line text-2xl"></i>
            </div>
            <div>
              <h1 class="text-2xl font-bold leading-tight">AkuntansiKu</h1>
              <p class="text-white/70 text-sm">Sistem Akuntansi Penjualan</p>
            </div>
          </div>

          <div class="mt-10 space-y-4">
            <div class="flex gap-3">
              <i class="fa-solid fa-shield-halved mt-1 text-emerald-300"></i>
              <p class="text-white/80 text-sm">Akses cepat, tampilan clean, dan laporan rapi.</p>
            </div>
            <div class="flex gap-3">
              <i class="fa-solid fa-bolt mt-1 text-yellow-300"></i>
              <p class="text-white/80 text-sm">Dashboard ringkas + jurnal otomatis.</p>
            </div>
            <div class="flex gap-3">
              <i class="fa-solid fa-file-invoice-dollar mt-1 text-sky-300"></i>
              <p class="text-white/80 text-sm">Laporan siap cetak kapan aja.</p>
            </div>
          </div>
        </div>

        <div class="text-xs text-white/50">
          © <span id="year"></span> AkuntansiKu. All rights reserved.
        </div>
      </div>

      <!-- Right Form -->
      <div class="rounded-3xl p-6 sm:p-10 bg-white shadow-2xl border border-slate-200 fade-in">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-slate-900">Masuk</h2>
            <p class="text-sm text-slate-500 mt-1">Login demo: <b>admin</b> / <b>admin123</b></p>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
            <i class="fa-solid fa-user-lock text-xl"></i>
          </div>
        </div>

        <form id="loginForm" class="mt-8 space-y-5">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <div class="relative">
              <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input id="username" type="text" required
                class="w-full rounded-xl border border-slate-200 bg-white px-10 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                placeholder="contoh: admin" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <div class="relative">
              <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input id="password" type="password" required
                class="w-full rounded-xl border border-slate-200 bg-white px-10 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                placeholder="••••••••" />
              <button type="button" onclick="togglePassword()"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit"
            class="w-full rounded-xl bg-indigo-700 text-white py-3 font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-800 transition">
            Masuk
          </button>

          <div class="mt-8 lg:hidden text-xs text-slate-400 text-center">
            © <span id="year2"></span> AkuntansiKu
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- =========================
       APP CONTAINER
  ========================== -->
  <div id="app-container" class="hidden h-screen overflow-hidden bg-slate-50">

    <!-- Mobile Topbar -->
    <header class="md:hidden sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200 no-print">
      <div class="px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <div class="leading-tight">
            <p class="font-bold text-slate-900">AkuntansiKu</p>
            <p class="text-[11px] text-slate-500 -mt-0.5">Sistem Penjualan</p>
          </div>
        </div>
        <button onclick="logout()" class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-rose-600 flex items-center justify-center">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
      </div>
    </header>

    <div class="h-full flex">
      <!-- Sidebar -->
      <aside id="sidebar"
        class="no-print fixed md:static inset-y-0 left-0 z-50 w-72 -translate-x-full md:translate-x-0 transition-transform
               bg-gradient-to-b from-slate-950 via-indigo-950 to-slate-900 text-white border-r border-white/10">
        <div class="h-full flex flex-col">

          <div class="p-6 border-b border-white/10">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                <i class="fa-solid fa-chart-line text-2xl"></i>
              </div>
              <div class="leading-tight">
                <h1 class="text-xl font-bold">AkuntansiKu</h1>
                <p class="text-xs text-white/60">Sistem Akuntansi Penjualan</p>
              </div>
            </div>
          </div>

          <nav class="flex-1 p-4 space-y-2">
            <button onclick="showPage('dashboard'); setActive(this)"
              class="nav-btn bg-white/10 w-full text-left px-4 py-3 rounded-2xl hover:bg-white/10 transition flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center"><i class="fa-solid fa-house"></i></span>
              <span class="font-medium">Dashboard</span>
            </button>

            <button onclick="showPage('pelanggan'); setActive(this)"
              class="nav-btn w-full text-left px-4 py-3 rounded-2xl hover:bg-white/10 transition flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center"><i class="fa-solid fa-users"></i></span>
              <span class="font-medium">Pelanggan</span>
            </button>

            <button onclick="showPage('transaksi'); setActive(this)"
              class="nav-btn w-full text-left px-4 py-3 rounded-2xl hover:bg-white/10 transition flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center"><i class="fa-solid fa-cart-shopping"></i></span>
              <span class="font-medium">Penjualan</span>
            </button>

            <button onclick="showPage('laporan'); setActive(this)"
              class="nav-btn w-full text-left px-4 py-3 rounded-2xl hover:bg-white/10 transition flex items-center gap-3">
              <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar"></i></span>
              <span class="font-medium">Laporan</span>
            </button>

            <div class="pt-4 mt-4 border-t border-white/10">
              <button onclick="logout()"
                class="w-full text-left px-4 py-3 rounded-2xl hover:bg-rose-600/20 transition flex items-center gap-3 text-rose-200">
                <span class="w-9 h-9 rounded-xl bg-rose-500/15 flex items-center justify-center">
                  <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </span>
                <span class="font-semibold">Logout</span>
              </button>
            </div>
          </nav>

          <div class="p-4 border-t border-white/10 text-xs text-white/50">
            <div class="flex items-center justify-between">
              <span>Version</span>
              <span class="font-semibold text-white/70">1.0</span>
            </div>
          </div>
        </div>
      </aside>

      <!-- Overlay mobile -->
      <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="md:hidden hidden fixed inset-0 bg-black/50 z-40 no-print"></div>

      <!-- Content -->
      <div class="flex-1 h-screen overflow-y-auto">

        <!-- Desktop Topbar -->
        <div class="hidden md:block sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 no-print">
          <div class="px-6 py-4 flex items-center justify-between">
            <div>
              <h2 id="page-title" class="text-lg font-bold text-slate-900">Dashboard</h2>
              <p class="text-xs text-slate-500 -mt-0.5">Ringkasan & aktivitas terbaru</p>
            </div>

            <div class="flex items-center gap-3">
              <button class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center hover:bg-slate-50 transition">
                <i class="fa-regular fa-bell"></i>
              </button>
              <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                  <i class="fa-solid fa-user"></i>
                </div>
                <div class="leading-tight">
                  <p class="text-sm font-semibold text-slate-900">Admin</p>
                  <p class="text-[11px] text-slate-500">Operator</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <main class="p-4 sm:p-6 lg:p-8">

          <!-- ===================== DASHBOARD ===================== -->
          <section id="dashboard" class="page-section fade-in">
            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-3">
              <div>
                <h3 class="text-2xl font-bold text-slate-900">Ringkasan Bisnis</h3>
                <p class="text-sm text-slate-500 mt-1">Pantau omset, laba, transaksi, dan pelanggan.</p>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                  <p class="text-sm text-slate-500">Omset</p>
                  <span class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                    <i class="fa-solid fa-coins"></i>
                  </span>
                </div>
                <h4 id="dash-omset" class="text-2xl font-bold mt-2 text-slate-900">Rp 0</h4>
                <p class="text-xs text-slate-500 mt-1">Total pendapatan penjualan.</p>
              </div>

              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                  <p class="text-sm text-slate-500">Laba Bersih</p>
                  <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-chart-simple"></i>
                  </span>
                </div>
                <h4 id="dash-profit" class="text-2xl font-bold mt-2 text-slate-900">Rp 0</h4>
                <p class="text-xs text-slate-500 mt-1">Pendapatan setelah biaya.</p>
              </div>

              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                  <p class="text-sm text-slate-500">Transaksi</p>
                  <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                    <i class="fa-solid fa-receipt"></i>
                  </span>
                </div>
                <h4 id="dash-transaksi" class="text-2xl font-bold mt-2 text-slate-900">0</h4>
                <p class="text-xs text-slate-500 mt-1">Jumlah transaksi tercatat.</p>
              </div>

              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                  <p class="text-sm text-slate-500">Pelanggan</p>
                  <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i class="fa-solid fa-users"></i>
                  </span>
                </div>
                <h4 id="dash-pelanggan" class="text-2xl font-bold mt-2 text-slate-900">0</h4>
                <p class="text-xs text-slate-500 mt-1">Total pelanggan terdaftar.</p>
              </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-5">
                <h4 class="font-bold text-slate-900">Jurnal Umum</h4>
                <p class="text-xs text-slate-500 mt-0.5">5 transaksi terakhir (double-entry).</p>
              </div>

              <div class="border-t border-slate-200 overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead class="bg-slate-50 text-slate-600">
                    <tr>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Tanggal</th>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Keterangan</th>
                      <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Debit</th>
                      <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Kredit</th>
                    </tr>
                  </thead>
                  <tbody id="jurnal-body" class="divide-y divide-slate-100"></tbody>
                </table>
              </div>
            </div>
          </section>

          <!-- ===================== PELANGGAN ===================== -->
          <section id="pelanggan" class="page-section hidden fade-in">
            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-3">
              <div>
                <h3 class="text-2xl font-bold text-slate-900">Pelanggan</h3>
                <p class="text-sm text-slate-500 mt-1">Tambah, cari, dan hapus pelanggan.</p>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-4">

              <!-- Form -->
              <div class="xl:col-span-1 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                  <h4 class="font-bold text-slate-900">Form Pelanggan</h4>
                  <p class="text-xs text-slate-500 mt-0.5">Isi data pelanggan baru.</p>
                </div>

                <form id="pelangganForm" class="p-5 space-y-4">
                  <div>
                    <label class="text-sm font-medium text-slate-700">Nama</label>
                    <input id="pelanggan_nama" required
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Nama pelanggan" />
                  </div>

                  <div>
                    <label class="text-sm font-medium text-slate-700">No HP</label>
                    <input id="pelanggan_hp"
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="08xxxxxxxxxx" />
                  </div>

                  <div>
                    <label class="text-sm font-medium text-slate-700">Alamat</label>
                    <textarea id="pelanggan_alamat" rows="3"
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Alamat pelanggan..."></textarea>
                  </div>

                  <button type="submit"
                    class="w-full px-4 py-3 rounded-xl bg-indigo-700 text-white hover:bg-indigo-800 transition text-sm font-semibold shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Simpan
                  </button>

                  <p id="pelangganFormMsg" class="text-xs text-slate-500"></p>
                </form>
              </div>

              <!-- List -->
              <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-200">
                  <div>
                    <h4 class="font-bold text-slate-900">Daftar Pelanggan</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Total: <span id="pelangganTotal" class="font-semibold">0</span> pelanggan</p>
                  </div>

                  <div class="relative w-full md:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="pelangganSearch" oninput="renderCustomers()"
                      class="w-full rounded-xl border border-slate-200 bg-white px-10 py-2.5 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Cari nama / hp..." />
                  </div>
                </div>

                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                      <tr>
                        <th class="text-left font-semibold px-5 py-3">#</th>
                        <th class="text-left font-semibold px-5 py-3">Nama</th>
                        <th class="text-left font-semibold px-5 py-3">HP</th>
                        <th class="text-left font-semibold px-5 py-3">Alamat</th>
                        <th class="text-right font-semibold px-5 py-3 no-print">Piutang</th>
                        <th class="text-right font-semibold px-5 py-3 no-print">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="pelangganTbody" class="divide-y divide-slate-100"></tbody>
                  </table>
                </div>

                <div id="pelangganEmpty" class="hidden p-10 text-center text-slate-500">
                  <p class="font-semibold text-slate-700">Belum ada pelanggan</p>
                  <p class="text-sm">Tambahkan pelanggan dulu.</p>
                </div>
              </div>
            </div>
          </section>

          <!-- ===================== PENJUALAN ===================== -->
          <section id="transaksi" class="page-section hidden fade-in">
            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-3">
              <div>
                <h3 class="text-2xl font-bold text-slate-900">Penjualan</h3>
                <p class="text-sm text-slate-500 mt-1">Input transaksi, hitung otomatis total & laba.</p>
              </div>
              <div class="flex gap-2 no-print">
                <button onclick="resetTransaksiForm()"
                  class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition text-sm font-semibold">
                  <i class="fa-solid fa-rotate-left mr-2"></i>Reset Form
                </button>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-4">

              <!-- Form -->
              <div class="xl:col-span-1 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200">
                  <h4 class="font-bold text-slate-900">Form Penjualan</h4>
                  <p class="text-xs text-slate-500 mt-0.5">Total & laba dihitung otomatis.</p>
                </div>

                <form id="transaksiForm" class="p-5 space-y-4">
                  <div>
                    <label class="text-sm font-medium text-slate-700">Tanggal</label>
                    <input id="trx_tanggal" type="date" required
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition" />
                  </div>

                  <div>
                    <label class="text-sm font-medium text-slate-700">Pelanggan</label>
                    <select id="trx_pelanggan_id"
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition">
                      <option value="">— Pilih pelanggan (opsional) —</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Kalau tidak pilih, isi nama manual.</p>
                  </div>

                  <div>
                    <label class="text-sm font-medium text-slate-700">Nama Pelanggan (manual)</label>
                    <input id="trx_nama_pelanggan" type="text"
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Contoh: Umum / Walk-in" />
                  </div>

                  <div>
                    <label class="text-sm font-medium text-slate-700">Nama Barang</label>
                    <input id="trx_barang" type="text" required
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Contoh: Kopi Susu" />
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="text-sm font-medium text-slate-700">Modal</label>
                      <input id="trx_modal" type="number" min="0" step="1" required
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                        placeholder="0" />
                    </div>
                    <div>
                      <label class="text-sm font-medium text-slate-700">Harga Jual</label>
                      <input id="trx_harga" type="number" min="0" step="1" required
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                        placeholder="0" />
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="text-sm font-medium text-slate-700">Qty</label>
                      <input id="trx_qty" type="number" min="1" step="1" required value="1"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition" />
                    </div>
                    <div>
                      <label class="text-sm font-medium text-slate-700">Metode</label>
                      <select id="trx_metode"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition">
                        <option value="Cash">Cash</option>
                        <option value="Credit">Credit</option>
                      </select>
                    </div>
                  </div>

                  <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3">
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Status Lunas</p>
                      <p class="text-[11px] text-slate-500">Cash otomatis lunas.</p>
                    </div>
                    <label class="inline-flex items-center gap-2">
                      <input id="trx_lunas" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-200">
                      <span class="text-sm text-slate-700">Lunas</span>
                    </label>
                  </div>

                  <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                      <p class="text-sm text-slate-600">Total</p>
                      <p id="trx_total_view" class="font-bold text-slate-900">Rp 0</p>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                      <p class="text-sm text-slate-600">Laba</p>
                      <p id="trx_laba_view" class="font-bold text-slate-900">Rp 0</p>
                    </div>
                  </div>

                  <button type="submit"
                    class="w-full px-4 py-3 rounded-xl bg-indigo-700 text-white hover:bg-indigo-800 transition text-sm font-semibold shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Simpan Transaksi
                  </button>

                  <p id="trxMsg" class="text-xs text-slate-500"></p>
                </form>
              </div>

              <!-- Table -->
              <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-200">
                  <div>
                    <h4 class="font-bold text-slate-900">Daftar Transaksi</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Total: <span id="trxTotalCount" class="font-semibold">0</span> transaksi</p>
                  </div>

                  <div class="relative w-full md:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="trxSearch" oninput="renderTransactionTable()"
                      class="w-full rounded-xl border border-slate-200 bg-white px-10 py-2.5 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition"
                      placeholder="Cari barang / pelanggan..." />
                  </div>
                </div>

                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                      <tr>
                        <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Tanggal</th>
                        <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Pelanggan</th>
                        <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Barang</th>
                        <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Total</th>
                        <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Laba</th>
                        <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Metode</th>
                        <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Status</th>
                        <th class="text-right font-semibold px-5 py-3 whitespace-nowrap no-print">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="trxTbody" class="divide-y divide-slate-100"></tbody>
                  </table>
                </div>

                <div id="trxEmpty" class="hidden p-10 text-center text-slate-500">
                  <p class="font-semibold text-slate-700">Belum ada transaksi</p>
                  <p class="text-sm">Input transaksi pertama untuk melihat jurnal & laporan.</p>
                </div>
              </div>

            </div>
          </section>

          <!-- ===================== LAPORAN ===================== -->
          <section id="laporan" class="page-section hidden fade-in">

            <!-- =========================
                 PRINT AREA (KHUSUS PDF)
            ========================== -->
            <div id="laporan-print" class="hidden">
              <div style="background:#fff; padding:16px; box-sizing:border-box; width:100%;">
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                  <div>
                    <div style="font-size:18px; font-weight:800; color:#0f172a;">Laporan AkuntansiKu</div>
                    <div id="lap_print_periode" style="font-size:12px; color:#64748b; margin-top:2px;">Periode: -</div>
                  </div>
                  <div style="text-align:right; font-size:12px; color:#64748b;">
                    <div id="lap_print_generated">Generated: -</div>
                  </div>
                </div>

                <hr style="margin:12px 0; border:none; border-top:1px solid #e2e8f0;" />

                <!-- Rekap 2 kolom biar GA kepotong -->
                <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-bottom:12px;">
                  <div style="border:1px solid #e2e8f0; border-radius:10px; padding:10px;">
                    <div style="font-size:11px; color:#64748b;">Omset</div>
                    <div id="lap_print_omset" style="font-size:14px; font-weight:800; color:#0f172a; margin-top:2px;">Rp 0</div>
                  </div>
                  <div style="border:1px solid #e2e8f0; border-radius:10px; padding:10px;">
                    <div style="font-size:11px; color:#64748b;">Laba</div>
                    <div id="lap_print_laba" style="font-size:14px; font-weight:800; color:#0f172a; margin-top:2px;">Rp 0</div>
                  </div>
                  <div style="border:1px solid #e2e8f0; border-radius:10px; padding:10px;">
                    <div style="font-size:11px; color:#64748b;">Transaksi</div>
                    <div id="lap_print_count" style="font-size:14px; font-weight:800; color:#0f172a; margin-top:2px;">0</div>
                  </div>
                  <div style="border:1px solid #e2e8f0; border-radius:10px; padding:10px;">
                    <div style="font-size:11px; color:#64748b;">Piutang</div>
                    <div id="lap_print_piutang" style="font-size:14px; font-weight:800; color:#0f172a; margin-top:2px;">Rp 0</div>
                  </div>
                </div>

                <table style="width:100%; border-collapse:collapse; font-size:11px;">
                  <thead>
                    <tr style="background:#f8fafc;">
                      <th style="text-align:left; padding:8px; border:1px solid #e2e8f0;">Tanggal</th>
                      <th style="text-align:left; padding:8px; border:1px solid #e2e8f0;">Pelanggan</th>
                      <th style="text-align:left; padding:8px; border:1px solid #e2e8f0;">Barang</th>
                      <th style="text-align:right; padding:8px; border:1px solid #e2e8f0;">Qty</th>
                      <th style="text-align:right; padding:8px; border:1px solid #e2e8f0;">Total</th>
                      <th style="text-align:right; padding:8px; border:1px solid #e2e8f0;">Laba</th>
                      <th style="text-align:left; padding:8px; border:1px solid #e2e8f0;">Metode</th>
                      <th style="text-align:left; padding:8px; border:1px solid #e2e8f0;">Status</th>
                    </tr>
                  </thead>
                  <tbody id="lap_print_tbody"></tbody>
                </table>

                <div style="margin-top:10px; font-size:10px; color:#94a3b8;">
                  © AkuntansiKu
                </div>
              </div>
            </div>
            <!-- =========================
                 /PRINT AREA
            ========================== -->

            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-3">
              <div>
                <h3 class="text-2xl font-bold text-slate-900">Laporan</h3>
                <p class="text-sm text-slate-500 mt-1">Filter tanggal, rekap, dan export CSV / PDF.</p>
              </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="text-sm font-medium text-slate-700">Dari Tanggal</label>
                  <input id="lap_from" type="date"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition" />
                </div>
                <div>
                  <label class="text-sm font-medium text-slate-700">Sampai Tanggal</label>
                  <input id="lap_to" type="date"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition" />
                </div>
                <div>
                  <label class="text-sm font-medium text-slate-700">Metode</label>
                  <select id="lap_metode"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-300 transition">
                    <option value="">Semua</option>
                    <option value="Cash">Cash</option>
                    <option value="Credit">Credit</option>
                  </select>
                </div>

                <!-- Tombol -->
                <div class="flex items-end gap-2">
                  <button onclick="updateReport()"
                    class="flex-1 px-4 py-3 rounded-xl bg-indigo-700 text-white hover:bg-indigo-800 transition text-sm font-semibold shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-filter mr-2"></i>Terapkan
                  </button>
                  <button onclick="resetLaporan()"
                    class="px-4 py-3 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition text-sm font-semibold">
                    Reset
                  </button>

                  <!-- ✅ PDF Button -->
                  <button onclick="downloadLaporanPDF()"
                    class="px-4 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition text-sm font-semibold">
                    <i class="fa-solid fa-file-pdf mr-2"></i> Download Laporan PDF
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">Omset</p>
                <p id="lap_omset" class="text-2xl font-bold text-slate-900 mt-1">Rp 0</p>
              </div>
              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">Laba</p>
                <p id="lap_laba" class="text-2xl font-bold text-slate-900 mt-1">Rp 0</p>
              </div>
              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">Transaksi</p>
                <p id="lap_count" class="text-2xl font-bold text-slate-900 mt-1">0</p>
              </div>
              <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <p class="text-sm text-slate-500">Piutang (Credit belum lunas)</p>
                <p id="lap_piutang" class="text-2xl font-bold text-slate-900 mt-1">Rp 0</p>
              </div>
            </div>

            <div class="mt-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
              <div class="p-5 border-b border-slate-200">
                <h4 class="font-bold text-slate-900">Detail Laporan</h4>
                <p class="text-xs text-slate-500 mt-0.5">Data transaksi sesuai filter.</p>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                  <thead class="bg-slate-50 text-slate-600">
                    <tr>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Tanggal</th>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Pelanggan</th>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Barang</th>
                      <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Qty</th>
                      <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Total</th>
                      <th class="text-right font-semibold px-5 py-3 whitespace-nowrap">Laba</th>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Metode</th>
                      <th class="text-left font-semibold px-5 py-3 whitespace-nowrap">Status</th>
                    </tr>
                  </thead>
                  <tbody id="lap_tbody" class="divide-y divide-slate-100"></tbody>
                </table>
              </div>

              <div id="lap_empty" class="hidden p-10 text-center text-slate-500">
                <p class="font-semibold text-slate-700">Data kosong</p>
                <p class="text-sm">Coba ubah filter tanggal/metode.</p>
              </div>
            </div>
          </section>

        </main>
      </div>
    </div>
  </div>

  <!-- =========================
       MODAL STRUK
  ========================== -->
  <div id="struk-overlay" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-2xl overflow-hidden">
      <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
          <h4 class="font-bold text-slate-900">Struk Penjualan</h4>
          <p class="text-xs text-slate-500">Preview transaksi.</p>
        </div>
        <button onclick="closeStruk()" class="w-10 h-10 rounded-xl border border-slate-200 hover:bg-slate-50 transition no-print">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="p-5 text-sm">
        <div class="text-center">
          <p class="font-bold text-slate-900 text-lg">AkuntansiKu</p>
          <p class="text-xs text-slate-500">Sistem Penjualan</p>
        </div>

        <div class="mt-4 space-y-2">
          <div class="flex justify-between">
            <span class="text-slate-500">Tanggal</span>
            <span id="struk_tanggal" class="font-semibold">-</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Pelanggan</span>
            <span id="struk_pelanggan" class="font-semibold">-</span>
          </div>

          <hr class="my-3">

          <div class="flex justify-between">
            <span>Barang</span>
            <span id="struk_barang" class="font-semibold">-</span>
          </div>
          <div class="flex justify-between">
            <span>Qty</span>
            <span id="struk_qty" class="font-semibold">-</span>
          </div>
          <div class="flex justify-between">
            <span>Harga</span>
            <span id="struk_harga" class="font-semibold">-</span>
          </div>

          <hr class="my-3">

          <div class="flex justify-between text-base">
            <span class="font-bold">Total</span>
            <span id="struk_total" class="font-bold">Rp 0</span>
          </div>

          <div class="flex justify-between text-xs text-slate-500">
            <span>Metode</span>
            <span id="struk_metode">-</span>
          </div>
          <div class="flex justify-between text-xs text-slate-500">
            <span>Status</span>
            <span id="struk_status">-</span>
          </div>

          <p class="text-center text-xs text-slate-500 mt-4">Terima kasih 🙏</p>
        </div>
      </div>

      <div class="p-4 bg-slate-50 border-t border-slate-200 flex gap-2 no-print">
        <button onclick="closeStruk()"
          class="px-4 py-3 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition text-sm font-semibold">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <!-- html2pdf -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <!-- script app -->
  <script src="assets/script.js"></script>

  <!-- UI Helpers (sidebar + active + password) -->
  <script>
    function togglePassword(){
      const el = document.getElementById('password');
      if(!el) return;
      el.type = el.type === 'password' ? 'text' : 'password';
    }

    function toggleSidebar(){
      const sb = document.getElementById('sidebar');
      const ov = document.getElementById('sidebar-overlay');
      if(!sb || !ov) return;
      const isOpen = !sb.classList.contains('-translate-x-full');
      if(isOpen){
        sb.classList.add('-translate-x-full');
        ov.classList.add('hidden');
      } else {
        sb.classList.remove('-translate-x-full');
        ov.classList.remove('hidden');
      }
    }

    function setActive(btn){
      document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('bg-white/10'));
      btn.classList.add('bg-white/10');
      if(window.innerWidth < 768) toggleSidebar();
    }

    // year
    document.getElementById('year')?.append(new Date().getFullYear());
    document.getElementById('year2')?.append(new Date().getFullYear());

    /* =========================
       ✅ DOWNLOAD PDF LAPORAN
    ========================== */
    async function downloadLaporanPDF(){
      // pastiin data laporan & print sudah keisi (kalau updateReport ada)
      if (typeof updateReport === 'function') {
        try { updateReport(); } catch(e) {}
      }

      const printEl = document.getElementById('laporan-print');
      if(!printEl){
        alert('Area print laporan tidak ditemukan (#laporan-print).');
        return;
      }

      const from = document.getElementById('lap_from')?.value || '-';
      const to   = document.getElementById('lap_to')?.value || '-';

      // metadata print
      const periodeEl = document.getElementById('lap_print_periode');
      if(periodeEl) periodeEl.textContent = `Periode: ${from} s/d ${to}`;

      const genEl = document.getElementById('lap_print_generated');
      if(genEl) genEl.textContent = 'Generated: ' + new Date().toLocaleString('id-ID');

      // tampilkan sementara
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
  </script>

</body>
</html>
