<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$summary_item = isset($loan_action_item) && is_object($loan_action_item) ? $loan_action_item : null;
if (!$summary_item) return;

$summary_details = isset($summary_item->detail_barang) && is_array($summary_item->detail_barang)
    ? $summary_item->detail_barang
    : [];
$summary_total_units = 0;
foreach ($summary_details as $summary_detail) {
    $summary_total_units += max(0, (int) ($summary_detail->jumlah_pinjam ?? 0));
}
if ($summary_total_units === 0 && isset($summary_item->total_jumlah)) {
    $summary_total_units = max(0, (int) $summary_item->total_jumlah);
}
$summary_total_types = count($summary_details);
if ($summary_total_types === 0 && isset($summary_item->total_jenis)) {
    $summary_total_types = max(0, (int) $summary_item->total_jenis);
}
$summary_days = function_exists('durasi_pinjam_hari') ? durasi_pinjam_hari($summary_item->tanggal_pinjam ?? null, $summary_item->tanggal_kembali_rencana ?? null) : 0;
$summary_identifier = $summary_item->group_id ?? $summary_item->id_peminjaman ?? '-';
$summary_show_status = !isset($loan_action_show_status) || $loan_action_show_status;
?>
<section class="loan-action-summary rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4 mb-4" aria-label="Ringkasan transaksi peminjaman">
    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
        <div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Peminjaman</div>
            <div class="text-base font-extrabold text-slate-900"><?= html_escape($summary_identifier) ?></div>
        </div>
        <?php if ($summary_show_status): ?>
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-200 text-slate-700"><?= html_escape($summary_item->status ?? '-') ?></span>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-3 gap-2 mb-3 text-center">
        <div class="bg-white rounded-xl border border-slate-200/80 p-2.5 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-400">Jenis</div>
            <strong class="text-sm font-extrabold text-slate-800"><?= $summary_total_types ?></strong>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-2.5 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-400">Total Unit</div>
            <strong class="text-sm font-extrabold text-slate-800"><?= $summary_total_units ?></strong>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-2.5 shadow-sm">
            <div class="text-[10px] uppercase font-bold text-slate-400">Durasi</div>
            <strong class="text-sm font-extrabold text-slate-800"><?= $summary_days > 0 ? $summary_days . ' hari' : '-' ?></strong>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs mb-3 bg-white p-3 rounded-xl border border-slate-200/80">
        <div><span class="text-slate-400">Peminjam:</span> <strong class="text-slate-800 block truncate"><?= html_escape($summary_item->nama_peminjam ?? '-') ?></strong></div>
        <div><span class="text-slate-400">NIM / NIP:</span> <strong class="text-slate-800 block font-mono"><?= html_escape($summary_item->nim_nip ?? '-') ?></strong></div>
        <div><span class="text-slate-400">Prodi:</span> <strong class="text-slate-800 block truncate"><?= html_escape($summary_item->prodi ?? $summary_item->prodi_peminjam ?? '-') ?></strong></div>
        <div><span class="text-slate-400">Kategori:</span> <strong class="text-slate-800 block"><?= html_escape($summary_item->jenis_peminjam ?? '-') ?></strong></div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-100/70 text-slate-600 font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2">Barang</th>
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Ruangan</th>
                    <th class="px-3 py-2 text-right">Unit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php if (empty($summary_details)): ?>
                <tr><td colspan="4" class="text-center text-slate-400 py-3 italic">Detail barang tidak tersedia.</td></tr>
            <?php else: foreach ($summary_details as $summary_detail): ?>
                <tr class="hover:bg-slate-50/50">
                    <td class="px-3 py-2 font-bold text-slate-800"><?= html_escape($summary_detail->nama_aset ?? '-') ?></td>
                    <td class="px-3 py-2 font-mono text-slate-500"><?= html_escape($summary_detail->kode_aset ?? '-') ?></td>
                    <td class="px-3 py-2 text-slate-600"><?= html_escape($summary_detail->nama_ruangan ?? '-') ?></td>
                    <td class="px-3 py-2 text-right font-extrabold text-slate-900"><?= (int) ($summary_detail->jumlah_pinjam ?? 0) ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>
