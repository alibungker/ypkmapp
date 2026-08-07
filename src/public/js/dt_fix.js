// DataTables global fix for peduli.ypkm.info
// Loads BEFORE DataTables lib so it can override defaults
(function () {
  // Inject CSS untuk pagination icon + header button text suppression
  var style = document.createElement('style');
  style.textContent =
    '/* DataTables pagination: icon-only buttons */\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button {\n' +
    '  font-size: 0 !important;\n' +
    '  padding: 0.35rem 0.5rem !important;\n' +
    '  min-width: 28px;\n' +
    '  border-radius: 6px !important;\n' +
    '}\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button::before {\n' +
    '  font-family: "Inter", sans-serif;\n' +
    '  font-size: 0.8rem !important;\n' +
    '  font-weight: 600 !important;\n' +
    '  line-height: 1.2;\n' +
    '}\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button.dt-paging-first::before { content: "«" !important; }\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button.dt-paging-last::before  { content: "»" !important; }\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button.dt-paging-next::before  { content: "›" !important; }\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button.dt-paging-previous::before { content: "‹" !important; font-size: 1rem !important; }\n' +
    '.dataTables_wrapper .dt-paging .dt-paging-button.current {\n' +
    '  color: #00034a !important;\n' +
    '  border-color: #00034a !important;\n' +
    '}\n' +
    '/* Suppress duplicate "Activate to sort" text in header buttons */\n' +
    'table.dataTable thead th button {\n' +
    '  display: inline-flex;\n' +
    '  align-items: center;\n' +
    '  gap: 4px;\n' +
    '  cursor: pointer;\n' +
    '}\n' +
    'table.dataTable thead th button span {\n' +
    '  display: none !important;\n' +
    '}\n' +
    '/* Info text */\n' +
    '.dataTables_wrapper .dt-info { font-size: 12px; color: #667085; font-weight: 500; }\n';
  document.head.appendChild(style);

  // Override global language (bisa dipakai semua tabel)
  if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
    window.jQuery.extend(true, window.jQuery.fn.DataTable.defaults, {
      language: {
        search: 'Cari:',
        searchPlaceholder: 'Cari...',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START–_END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data',
        infoFiltered: '(dari _MAX_ data)',
        zeroRecords: 'Data tidak ditemukan',
        emptyTable: 'Belum ada data',
        paginate: {}  // Empty = no text, CSS ::before handles icons
      },
      // Bersihkan aria-label default DataTables untuk cegah duplikat
      createdRow: function (row, data, dataIndex) {
        // rows processed
      }
    });
  }

  // MutationObserver untuk bersihkan aria-label setelah DataTables render
  var observer = new MutationObserver(function (mutations) {
    var ths = document.querySelectorAll('table.dataTable thead th');
    ths.forEach(function (th) {
      var btn = th.querySelector('button');
      if (btn && btn.getAttribute('aria-label')) {
        // Bersihkan aria-label berisi ": Activate to sort/invert"
        var label = btn.getAttribute('aria-label') || '';
        if (/Activate to (sort|invert sorting)/i.test(label)) {
          btn.setAttribute('aria-label', th.textContent.trim());
        }
      }
      // Also suppress any visible span inside the sort button
      var span = th.querySelector('button span');
      if (span) { span.style.display = 'none'; }
    });
  });
  observer.observe(document.body, { childList: true, subtree: true });
})();
