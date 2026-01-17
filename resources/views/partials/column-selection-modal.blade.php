<!-- Column Selection Modal -->
<div class="modal" id="columnModal">
  <div class="modal-content column-modal-vertical">
    <div class="modal-header">
      <h4>Column Select & Sort</h4>
      <div class="modal-header-buttons">
        <button class="btn-save-orange" onclick="saveColumnSettings()">Save</button>
        <button class="btn-cancel-gray" onclick="closeColumnModal()">Cancel</button>
      </div>
    </div>
    <div class="modal-body">
      <form id="columnForm" action="{{ $columnSettingsRoute }}" method="POST">
        @csrf
        <div class="column-selection-vertical" id="columnSelection">
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
            $counter = 1;
          @endphp
          @foreach($ordered as $key => $label)
            @php
              $isMandatory = in_array($key, $mandatoryFields);
              $isChecked = in_array($key, $selectedColumns) || $isMandatory;
            @endphp
            <div class="column-item-vertical" draggable="true" data-column="{{ $key }}">
              <span class="column-number">{{ $counter }}</span>
              <label class="column-label-wrapper">
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                <span class="column-label-text">{{ $label }}</span>
              </label>
            </div>
            @php $counter++; @endphp
          @endforeach
        </div>
      </form>
      <div class="column-drag-hint">Drag and Select to position and display</div>
    </div>
  </div>
</div>

