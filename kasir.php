<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GlowSalon - POS & Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body class="bg-pink-50 font-sans text-gray-700">
    <nav class="bg-pink-600 text-white p-4 flex justify-between items-center shadow-md">
        <h1 class="font-bold text-xl">GlowSalon POS 💖 (Admin: <?php echo $_SESSION['user']; ?>)</h1>
        <div class="space-x-2">
            <a href="kasir.php?page=kasir" class="px-3 py-1 bg-pink-700 rounded hover:bg-pink-800">Kasir</a>
            <a href="kasir.php?page=dashboard" class="px-3 py-1 bg-pink-700 rounded hover:bg-pink-800">Dashboard Admin</a>
            <a href="kasir.php?logout=true" class="px-3 py-1 bg-red-500 rounded hover:bg-red-600">Keluar</a>
        </div>
    </nav>

    <?php if ($page == 'kasir'): ?>
        <div class="p-6 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold text-lg text-pink-600 mb-2">📸 Hardware Sensor: Pemindai Kamera Barcode/QR</h3>
                    <div id="reader" class="w-full max-w-sm mx-auto border rounded-lg p-2 bg-gray-50"></div>
                </div>
                
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="font-bold text-lg text-pink-600 mb-3">✨ Katalog Layanan & Produk</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-72 overflow-y-auto p-1">
                        <button onclick="addToCart('Hair Styling & Blow', 85000)" class="p-3 border rounded-lg hover:bg-pink-50 text-left">
                            <span class="font-bold block text-sm">Hair Styling & Blow</span>
                            <p class="text-pink-600 font-semibold text-xs mt-1">Rp 85.000</p>
                        </button>
                        <button onclick="addToCart('Hair Serum Rose 100ml', 150000)" class="p-3 border rounded-lg hover:bg-purple-50 text-left">
                            <span class="font-bold block text-sm">Hair Serum Rose</span>
                            <p class="text-pink-600 font-semibold text-xs mt-1">Rp 150.000</p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl shadow h-fit space-y-4">
                <h3 class="font-bold text-lg text-pink-600">Keranjang Transaksi</h3>
                <ul id="cart-list" class="divide-y text-sm mb-4 min-h-[120px]">
                    <li class="py-2 text-gray-400">Keranjang masih kosong</li>
                </ul>
                <div class="border-t pt-2 font-bold text-lg flex justify-between">
                    <span>Total:</span>
                    <span id="total-price" class="text-pink-600">Rp 0</span>
                </div>
                <button onclick="prosesBayar()" class="w-full bg-green-500 text-white py-2.5 rounded-lg font-semibold hover:bg-green-600">Proses Transaksi</button>
                <button onclick="clearCart()" class="w-full bg-gray-200 text-gray-700 py-1.5 rounded-lg text-xs">Kosongkan</button>
            </div>
        </div>
    <?php else: ?>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-pink-600">Dashboard & Rekapitulasi Penjualan</h2>
                <button onclick="exportToExcel()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold shadow">📄 Ekspor ke Excel (.xlsx)</button>
            </div>
            <div class="bg-white p-4 rounded-xl shadow overflow-x-auto">
                <table id="rekap-table" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-pink-100 text-pink-700 text-sm">
                            <th class="p-3">No. Nota</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Kasir</th>
                            <th class="p-3">Total Transaksi</th>
                            <th class="p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b">
                            <td class="p-3 font-semibold">TRX-20261001</td>
                            <td class="p-3">07 Okt 2026</td>
                            <td class="p-3"><?php echo $_SESSION['user']; ?></td>
                            <td class="p-3 font-semibold text-pink-600">Rp 235.000</td>
                            <td class="p-3"><button onclick="window.print()" class="text-xs bg-gray-200 px-3 py-1 rounded">Cetak Struk</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <script>
        let cart = [];
        function addToCart(nama, harga) {
            cart.push({nama, harga});
            renderCart();
        }
        function clearCart() { cart = []; renderCart(); }
        function renderCart() {
            const list = document.getElementById('cart-list');
            let total = 0;
            if(cart.length === 0) {
                list.innerHTML = `<li class="py-2 text-gray-400">Keranjang masih kosong</li>`;
            } else {
                list.innerHTML = '';
                cart.forEach((item) => {
                    total += item.harga;
                    list.innerHTML += `<li class="py-2 flex justify-between items-center"><span>${item.nama}</span><b class="text-xs">Rp ${item.harga.toLocaleString('id-ID')}</b></li>`;
                });
            }
            document.getElementById('total-price').innerText = `Rp ${total.toLocaleString('id-ID')}`;
        }
        function prosesBayar() {
            if(cart.length === 0) return alert('Keranjang masih kosong!');
            alert('Transaksi Berhasil Disimpan!');
            cart = [];
            renderCart();
        }
        <?php if ($page == 'kasir'): ?>
        document.addEventListener("DOMContentLoaded", function() {
            const scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 200 });
            scanner.render((decodedText) => {
                alert(`Barcode Berhasil Discan: ${decodedText}`);
                addToCart(`Produk Scan (${decodedText})`, 100000);
            }, (error) => {});
        });
        <?php endif; ?>
        function exportToExcel() {
            const table = document.getElementById("rekap-table");
            const wb = XLSX.utils.table_to_book(table);
            XLSX.writeFile(wb, "Rekap_Penjualan_GlowSalon.xlsx");
        }
    </script>
</body>
</html>