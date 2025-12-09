<style>
    * { box-sizing: border-box; }
    .dashboard { padding-left:0 !important; }
    body { font-family: Arial, sans-serif; color: #000; margin: 0; background: #f5f5f5; }
    .container-table { max-width: 100%; margin: 0 auto; background: #fff; padding: 0; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0; flex-wrap: wrap; gap: 15px; background: #f5f5f5; padding: 15px 20px; border-bottom: 1px solid #ddd; }
    .page-title-section { display: flex; align-items: center; gap: 15px; flex: 1; }
    h3 { background: transparent; padding: 0; margin: 0; font-weight: bold; color: #2d2d2d; font-size: 24px; }
    .records-found { font-size: 14px; color: #2d2d2d; font-weight: normal; }
    .top-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px; margin-bottom:15px; }
    .left-group { display:flex; align-items:center; gap:15px; flex:1 1 auto; min-width:220px; }
    .left-buttons { display:flex; gap:10px; align-items:center; }
    .filter-group { display:flex; align-items:center; gap:8px; }
    .filter-panel { display:none; background:#f5f5f5; padding:12px 15px; border-radius:4px; margin-top:10px; border:1px solid #ddd; }
    .filter-panel.active { display:block; }
    .column-filter { display:none; }
    .column-filter.visible { display:block; }
    .action-buttons { margin-left:auto; display:flex; gap:10px; align-items:center; }
    .btn { border:none; cursor:pointer; padding:6px 16px; font-size:13px; border-radius:2px; white-space:nowrap; transition:background-color .2s; text-decoration:none; color:inherit; background:#fff; border:1px solid #ccc; font-weight:normal; }
    .btn-add { background:#f3742a; color:#fff; border-color:#f3742a; }
    .btn-export, .btn-column { background:#fff; color:#000; border:1px solid #ccc; }
    .btn-archived { background:#2d2d2d; color:#fff; border-color:#2d2d2d; }
    .btn-archived.active { background:#f3742a; border-color:#f3742a; }
    .btn-follow-up { background:#2d2d2d; color:#fff; border-color:#2d2d2d; }
    .btn-list-all { background:#4CAF50; color:#fff; border-color:#4CAF50; }
    .btn-close { background:#e0e0e0; color:#000; border-color:#ccc; }
    .btn-back { background:#ccc; color:#333; border-color:#ccc; }
    .filter-toggle { display:flex; align-items:center; gap:8px; }
    .toggle-switch { position:relative; width:44px; height:24px; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:24px; }
    .toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
    .toggle-switch input:checked + .toggle-slider { background-color:#4CAF50; }
    .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
    .table-responsive { width: 100%; border: none; background: #fff; margin-bottom:0; overflow-x: auto; padding: 0 20px; }
    .table-responsive.no-scroll { overflow: visible; }
    .footer { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; gap:10px; border-top:1px solid #ddd; flex-wrap:wrap; margin-top:0; background:#f5f5f5; }
    .footer-left { display:flex; gap:10px; }
   .paginator {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: #555;
      white-space: nowrap;
      margin: 0 auto;
    }
    .btn-page{
      color: #2d2d2d;
      font-size: 14px;
      width: 32px;
      height: 32px;
      padding: 0;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      border: 1px solid #ddd;
      border-radius: 2px;
      font-weight: normal;
    }
    .btn-page:hover:not([disabled]) { background: #e9e9e9; }
    .btn-page[disabled] { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; }
    .page-info { padding: 0 12px; color: #555; font-size: 12px; }
    table { width:100%; border-collapse:collapse; font-size:13px; min-width:900px; }
    thead tr { background-color: #000; color: #fff; height:35px; font-weight: normal; }
    thead th { padding:8px 5px; text-align:left; border-right:1px solid #444; white-space:nowrap; font-weight: normal; color: #fff !important; }
    thead th:first-child { text-align:center; }
    thead th:last-child { border-right:none; }
    tbody tr { background-color:#fff; border-bottom:1px solid #ddd; min-height:32px; }
    tbody tr:nth-child(even) { background-color:#f8f8f8; }
    tbody tr.archived-row { background:#fff3cd !important; }
    tbody tr.archived-row td { background:#fff3cd !important; }
    tbody tr.inactive-row { background:#fff3cd !important; }
    tbody tr.inactive-row td { background:#fff3cd !important; }
    tbody td { padding:8px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    .action-cell { display:flex; align-items:center; gap:10px; padding:8px; }
    .action-expand { width:22px; height:22px; cursor:pointer; display:inline-block; }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    /* Full Page View Styles */
    .client-page-view { display:none; width:100%; min-height:calc(100vh - 60px); background:#f5f5f5; overflow-y:auto; -webkit-overflow-scrolling:touch; }
    .client-page-view.show { display:block !important; }
    .clients-table-view { display:block; width:100%; }
    .clients-table-view.hidden { display:none; }
    .client-page-header { background:#fff; color:#000; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd; position:sticky; top:0; z-index:10; box-shadow:0 2px 4px rgba(0,0,0,0.1); }
    .client-page-title { font-size:20px; font-weight:bold; color:#000; display:flex; align-items:center; gap:8px; }
    .client-page-title .client-name { color:#f3742a; }
    .client-page-nav { display:flex; gap:8px; }
    .nav-tab { background:#2d2d2d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px; transition:background 0.2s; }
    .nav-tab:hover { background:#444; }
    .nav-tab.active { background:#2d2d2d; border-bottom:3px solid #4CAF50; }
    .client-page-actions { display:flex; gap:8px; }
    .client-page-body { padding-top:20px; background:#f5f5f5; min-height:calc(100vh - 60px); overflow-y:auto; -webkit-overflow-scrolling:touch; }
    .client-page-content { background:transparent; padding:0; overflow-y:auto; -webkit-overflow-scrolling:touch; }
    .detail-section { background:#fff; margin-bottom:0; border-radius:0; overflow:hidden; margin-right:10px; }
    .detail-section:last-child { margin-right:0; }
    .detail-section-header { background:#a0a0a0; color:#fff; padding:6px 10px; font-weight:bold; font-size:12px; border-bottom:1px solid #ddd; text-transform:uppercase; line-height:1.4; }
    .detail-section-body { padding:8px; }
    .detail-row { display:flex !important; flex-direction:row !important; align-items:center; margin-bottom:8px; gap:8px; }
    .detail-row:last-child { margin-bottom:0; }
    .detail-label { font-size:10px; color:#555; font-weight:600; line-height:1.2; display:block; min-width:120px; flex-shrink:0; }
    .detail-value { font-size:11px; color:#000; padding:4px 6px; border:1px solid #ddd; background:#fff; border-radius:2px; min-height:22px; display:flex; align-items:center; box-sizing:border-box; flex:1; }
    .detail-value.checkbox { border:none; padding:0; background:transparent; min-height:auto; flex:0 0 auto; }
    .detail-value.checkbox input[type="checkbox"] { 
      width:18px; 
      height:18px;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      border:1px solid #ddd;
      border-radius:3px;
      background:#fff;
      position:relative;
      margin:0;
      cursor:default;
    }
    .detail-value.checkbox input[type="checkbox"]:checked {
      background-color:#f3742a;
      border-color:#f3742a;
    }
    .detail-value.checkbox input[type="checkbox"]:checked::after {
      content:'✓';
      position:absolute;
      top:50%;
      left:50%;
      transform:translate(-50%, -50%);
      color:#fff;
      font-size:12px;
      font-weight:bold;
    }
    /* Modal styles */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
    .modal.show { display:flex; }
    .modal-content { background:#fff; border-radius:6px; width:92%; max-width:1100px; max-height:calc(100vh - 40px); overflow:auto; box-shadow:0 4px 6px rgba(0,0,0,.1); padding:0; }
    .modal-header { padding:12px 15px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; background:white; }
    .modal-body { padding:15px; }
    .modal-close { background:none; border:none; font-size:18px; cursor:pointer; color:#666; }
    .modal-footer { padding:12px 15px; border-top:1px solid #ddd; display:flex; justify-content:flex-end; gap:8px; background:#f9f9f9; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; margin-bottom: 4px; font-weight: bold; font-size: 13px; }
    .form-control { width: 100%; padding: 6px 8px; border: 1px solid #ccc; border-radius: 2px; font-size: 13px; }
    .form-row { display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; align-items: flex-start; }
    .form-row .form-group { flex: 0 0 calc((100% - 20px) / 3); margin-bottom: 0; }
    .btn-save { background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .btn-cancel { background: #6c757d; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .btn-delete { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .alert { padding: 8px 12px; margin-bottom: 12px; border-radius: 2px; font-size: 13px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    /* Column Selection Styles */
    .column-selection { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; margin-bottom: 15px; }
    .column-item { display: flex; align-items: center; gap: 8px; padding: 6px 8px; border: 1px solid #ddd; border-radius: 2px; cursor: move; transition: all 0.2s; }
    .column-item:hover { background: #f5f5f5; }
    .column-item.selected { background: #007bff; color: white; border-color: #007bff; }
    .column-item input[type="checkbox"] { margin: 0; }
    .column-actions { display: flex; gap: 8px; margin-bottom: 15px; }
    .btn-select-all { background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; font-size: 12px; }
    .btn-deselect-all { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; font-size: 12px; }
    /* Drag and drop styles */
    .column-item.dragging { opacity:0.5; background:#e3f2fd; border-color:#2196F3; }
    .column-item.drag-over { border-top:2px solid #2196F3; }
    @media print {
      /* Hide all page elements except table */
      .page-header, .footer, .action-buttons, .btn, .modal,
      .dashboard > .container-table > .page-header,
      .dashboard > .container-table > .footer,
      .records-found, .filter-group, h3 { display: none !important; }
      
      /* Show only table container */
      .table-responsive { 
        display: block !important; 
        overflow: visible !important; 
        border: none !important; 
        padding: 0 !important; 
        margin: 0 !important;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
      }
      
      /* Table styling */
      table { 
        width: 100% !important; 
        page-break-inside: auto; 
        border-collapse: collapse !important;
        font-size: 11px !important;
      }
      
      /* Header styling */
      thead { display: table-header-group !important; }
      thead th { 
        background-color: #000 !important; 
        color: #fff !important; 
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        border: 1px solid #333 !important;
        padding: 8px 5px !important;
      }
      
      /* Body styling */
      tbody tr { 
        page-break-inside: avoid; 
        page-break-after: auto; 
        border-bottom: 1px solid #ddd !important;
      }
      tbody td { 
        border: 1px solid #ddd !important; 
        padding: 6px 5px !important;
      }
      
      .action-cell { 
        display: table-cell !important; 
        width: auto !important;
        padding: 6px 5px !important;
        border: 1px solid #ddd !important;
      }
      .action-cell svg, .action-expand { 
        display: inline-block !important; 
        visibility: visible !important;
        width: 22px !important;
        height: 22px !important;
      }
      
      /* Page settings */
      @page { 
        margin: 1cm; 
        size: A4 landscape;
      }
      
      /* Hide sidebar and other layout elements */
      .sidebar, .main-content > .top-header { display: none !important; }
    }
    @media (max-width: 768px) { 
      .form-row .form-group { flex: 0 0 calc((100% - 20px) / 2); } 
      .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
      .action-buttons { width: 100%; justify-content: flex-start; }
      .footer { flex-direction: column; align-items: stretch; gap: 10px; }
    }
  </style>

