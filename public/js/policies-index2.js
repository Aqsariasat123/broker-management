
        let currentPolicyId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Column Settings Modal
            const columnSettingsBtn = document.getElementById('columnSettingsBtn');
            if (columnSettingsBtn) {
                columnSettingsBtn.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('columnSettingsModal'));
                    modal.show();
                });
            }

            // Add Policy Button
            const addPolicyBtn = document.getElementById('addPolicyBtn');
            if (addPolicyBtn) {
                addPolicyBtn.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('addPolicyModal'));
                    modal.show();
                });
            }

            // DFR Only Filter
            const dfrOnlyBtn = document.getElementById('dfrOnlyBtn');
            if (dfrOnlyBtn) {
                let showDfrOnly = false;
                
                dfrOnlyBtn.addEventListener('click', function() {
                    showDfrOnly = !showDfrOnly;
                    const rows = document.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        if (showDfrOnly) {
                            if (row.classList.contains('dfr-row')) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                            dfrOnlyBtn.style.backgroundColor = '#dc3545';
                            dfrOnlyBtn.textContent = 'Show All';
                        } else {
                            row.style.display = '';
                            dfrOnlyBtn.style.backgroundColor = 'black';
                            dfrOnlyBtn.textContent = 'Due For Renewal';
                        }
                    });
                });
            }

            // Edit from details button handler
            document.getElementById('editFromDetailsBtn')?.addEventListener('click', function() {
                if (currentPolicyId) {
                    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('policyDetailsModal'));
                    detailsModal.hide();
                    editPolicy(currentPolicyId);
                }
            });

            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });

        function showPolicyDetails(policyId) {
            currentPolicyId = policyId;
            
            // Show loading state
            document.getElementById('policyDetailsBody').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading policy details...</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('policyDetailsModal'));
            modal.show();

            // Simulate API call - in real scenario, you'd fetch from server
            setTimeout(() => {
                const policy = getPolicyById(policyId);
                if (policy) {
                    document.getElementById('policyDetailsBody').innerHTML = generatePolicyDetailsHTML(policy);
                } else {
                    document.getElementById('policyDetailsBody').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading policy details
                        </div>
                    `;
                }
            }, 500);
        }

        function getPolicyById(policyId) {
            // This is a mock function - in real scenario, you'd fetch from server
            const policies = {!! $policies->toJson() !!};
            return policies.find(p => p.id === policyId);
        }

        function generatePolicyDetailsHTML(policy) {
            return `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="40%" class="bg-light">Policy No:</th>
                                <td>${policy.policy_no}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Client Name:</th>
                                <td>${policy.client_name}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Insurer:</th>
                                <td>${policy.insurer}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Policy Class:</th>
                                <td>${policy.policy_class}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Policy Plan:</th>
                                <td>${policy.policy_plan}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Sum Insured:</th>
                                <td>${policy.sum_insured ? new Intl.NumberFormat().format(policy.sum_insured) : 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="40%" class="bg-light">Start Date:</th>
                                <td>${new Date(policy.start_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">End Date:</th>
                                <td>${new Date(policy.end_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Policy Status:</th>
                                <td>
                                    <span class="badge bg-${policy.policy_status == 'In Force' ? 'success' : (policy.policy_status == 'DFR' ? 'warning' : (policy.policy_status == 'Expired' ? 'secondary' : 'danger'))}">
                                        ${policy.policy_status}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Date Registered:</th>
                                <td>${new Date(policy.date_registered).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Policy ID:</th>
                                <td>${policy.policy_id}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Renewable:</th>
                                <td>${policy.renewable}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="40%" class="bg-light">Business Type:</th>
                                <td>${policy.biz_type}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Term:</th>
                                <td>${policy.term} ${policy.term_unit}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Base Premium:</th>
                                <td>${new Intl.NumberFormat().format(policy.base_premium)}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Premium:</th>
                                <td>${new Intl.NumberFormat().format(policy.premium)}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="40%" class="bg-light">Frequency:</th>
                                <td>${policy.frequency}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Pay Plan:</th>
                                <td>${policy.pay_plan}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Agency:</th>
                                <td>${policy.agency || 'N/A'}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Agent:</th>
                                <td>${policy.agent || 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                ${policy.notes ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="20%" class="bg-light">Notes:</th>
                                <td>${policy.notes}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                ` : ''}
            `;
        }

        function editPolicy(policyId) {
            currentPolicyId = policyId;
            
            // Show loading state
            document.getElementById('editPolicyModalBody').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading policy data...</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('editPolicyModal'));
            modal.show();

            // Simulate API call - in real scenario, you'd fetch from server
            setTimeout(() => {
                const policy = getPolicyById(policyId);
                if (policy) {
                    document.getElementById('editPolicyModalBody').innerHTML = generateEditFormHTML(policy);
                    document.getElementById('editPolicyForm').action = `/policies/${policyId}`;
                } else {
                    document.getElementById('editPolicyModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading policy data
                        </div>
                    `;
                }
            }, 500);
        }

        function generateEditFormHTML(policy) {
            const lookupData = {!! json_encode($lookupData) !!};
            
            return `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit_policy_no" class="form-label">Policy No *</label>
                            <input type="text" class="form-control" id="edit_policy_no" name="policy_no" value="${policy.policy_no}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_client_name" class="form-label">Client Name *</label>
                            <input type="text" class="form-control" id="edit_client_name" name="client_name" value="${policy.client_name}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_insurer" class="form-label">Insurer *</label>
                            <select class="form-select" id="edit_insurer" name="insurer" required>
                                <option value="">Select Insurer</option>
                                ${lookupData.insurers.map(insurer => `
                                    <option value="${insurer}" ${policy.insurer === insurer ? 'selected' : ''}>${insurer}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_policy_class" class="form-label">Policy Class *</label>
                            <select class="form-select" id="edit_policy_class" name="policy_class" required>
                                <option value="">Select Policy Class</option>
                                ${lookupData.policy_classes.map(cls => `
                                    <option value="${cls}" ${policy.policy_class === cls ? 'selected' : ''}>${cls}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_policy_plan" class="form-label">Policy Plan *</label>
                            <select class="form-select" id="edit_policy_plan" name="policy_plan" required>
                                <option value="">Select Policy Plan</option>
                                ${lookupData.policy_plans.map(plan => `
                                    <option value="${plan}" ${policy.policy_plan === plan ? 'selected' : ''}>${plan}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sum_insured" class="form-label">Sum Insured</label>
                            <input type="number" step="0.01" class="form-control" id="edit_sum_insured" name="sum_insured" value="${policy.sum_insured || ''}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" value="${policy.start_date.split(' ')[0]}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" value="${policy.end_date.split(' ')[0]}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit_insured" class="form-label">Insured</label>
                            <input type="text" class="form-control" id="edit_insured" name="insured" value="${policy.insured || ''}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_policy_status" class="form-label">Policy Status *</label>
                            <select class="form-select" id="edit_policy_status" name="policy_status" required>
                                <option value="">Select Status</option>
                                ${lookupData.policy_statuses.map(status => `
                                    <option value="${status}" ${policy.policy_status === status ? 'selected' : ''}>${status}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date_registered" class="form-label">Date Registered *</label>
                            <input type="date" class="form-control" id="edit_date_registered" name="date_registered" value="${policy.date_registered.split(' ')[0]}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_policy_id" class="form-label">Policy ID *</label>
                            <input type="text" class="form-control" id="edit_policy_id" name="policy_id" value="${policy.policy_id}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_insured_item" class="form-label">Insured Item</label>
                            <input type="text" class="form-control" id="edit_insured_item" name="insured_item" value="${policy.insured_item || ''}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_renewable" class="form-label">Renewable *</label>
                            <select class="form-select" id="edit_renewable" name="renewable" required>
                                <option value="">Select Option</option>
                                ${lookupData.renewable_options.map(option => `
                                    <option value="${option}" ${policy.renewable === option ? 'selected' : ''}>${option}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_biz_type" class="form-label">Business Type *</label>
                            <select class="form-select" id="edit_biz_type" name="biz_type" required>
                                <option value="">Select Business Type</option>
                                ${lookupData.biz_types.map(type => `
                                    <option value="${type}" ${policy.biz_type === type ? 'selected' : ''}>${type}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_term" class="form-label">Term *</label>
                                    <input type="number" class="form-control" id="edit_term" name="term" value="${policy.term}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_term_unit" class="form-label">Term Unit *</label>
                                    <select class="form-select" id="edit_term_unit" name="term_unit" required>
                                        <option value="">Select Unit</option>
                                        ${lookupData.term_units.map(unit => `
                                            <option value="${unit}" ${policy.term_unit === unit ? 'selected' : ''}>${unit}</option>
                                        `).join('')}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_base_premium" class="form-label">Base Premium *</label>
                            <input type="number" step="0.01" class="form-control" id="edit_base_premium" name="base_premium" value="${policy.base_premium}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_premium" class="form-label">Premium *</label>
                            <input type="number" step="0.01" class="form-control" id="edit_premium" name="premium" value="${policy.premium}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_frequency" class="form-label">Frequency *</label>
                            <select class="form-select" id="edit_frequency" name="frequency" required>
                                <option value="">Select Frequency</option>
                                ${lookupData.frequencies.map(freq => `
                                    <option value="${freq}" ${policy.frequency === freq ? 'selected' : ''}>${freq}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_pay_plan" class="form-label">Pay Plan *</label>
                            <select class="form-select" id="edit_pay_plan" name="pay_plan" required>
                                <option value="">Select Pay Plan</option>
                                ${lookupData.pay_plans.map(plan => `
                                    <option value="${plan}" ${policy.pay_plan === plan ? 'selected' : ''}>${plan}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_agency" class="form-label">Agency</label>
                            <input type="text" class="form-control" id="edit_agency" name="agency" value="${policy.agency || ''}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_agent" class="form-label">Agent</label>
                            <input type="text" class="form-control" id="edit_agent" name="agent" value="${policy.agent || ''}">
                        </div>
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3">${policy.notes || ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
        }

        function confirmDelete(policyId) {
            if (confirm('Are you sure you want to delete this policy?')) {
                document.getElementById('delete-form-' + policyId).submit();
            }
        }

        function exportPolicies() {
            window.location.href = policiesExportRoute;
        }

        function saveColumnSettings() {
            document.getElementById('columnSettingsForm').submit();
        }

        // Form validation
        document.getElementById('addPolicyForm')?.addEventListener('submit', function(e) {
            if (!validatePolicyForm(this)) {
                e.preventDefault();
            }
        });

        document.getElementById('editPolicyForm')?.addEventListener('submit', function(e) {
            if (!validatePolicyForm(this)) {
                e.preventDefault();
            }
        });

        function validatePolicyForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                alert('Please fill all required fields');
            }

            return isValid;
        }
    