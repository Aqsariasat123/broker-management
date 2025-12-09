<!-- Column Selection Modal -->
<div class="modal" id="columnModal">
  <div class="modal-content">
    <div class="modal-header">
      <h4>Column Select & Sort</h4>
      <button type="button" class="modal-close" onclick="closeColumnModal()">×</button>
    </div>
    <div class="modal-body">
      <div class="column-actions">
        <button class="btn-select-all" onclick="selectAllColumns()">Select All</button>
        <button class="btn-deselect-all" onclick="deselectAllColumns()">Deselect All</button>
      </div>

      <form id="columnForm" action="{{ $columnSettingsRoute }}" method="POST">
        @csrf
        <div class="column-selection" id="columnSelection">
          @php
            // Maintain order based on selectedColumns
            $ordered = [];
            foreach($selectedColumns as $col) {
              if(isset($columnDefinitions[$col])) {
                $ordered[$col] = $columnDefinitions[$col];
                unset($columnDefinitions[$col]);
              }
            }
            $ordered = array_merge($ordered, $columnDefinitions);
          @endphp

          @php
            // Mandatory fields that should always be checked and disabled
            $mandatoryFields = $mandatoryColumns ?? [];
          @endphp
          @foreach($ordered as $key => $label)
            @php
              $isMandatory = in_array($key, $mandatoryFields);
              $isChecked = in_array($key, $selectedColumns) || $isMandatory;
            @endphp
            <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
              <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
              <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
              <label for="col_{{ $key }}" style="cursor:pointer; flex:1; user-select:none;">{{ $label }}</label>
            </div>
          @endforeach
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeColumnModal()">Cancel</button>
      <button class="btn-save" onclick="saveColumnSettings()">Save Settings</button>
    </div>
  </div>
</div>

