@extends('layout.admin')

@section('head')
    <link href="{{ asset('css/ho-dashboard.css') }}" rel="stylesheet">

    <style>
        /* Modal animation */
        .branch-modal-overlay {
            animation: fadeIn 0.3s ease-out;
        }
        
        .branch-modal {
            animation: slideUp 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(50px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }
        
        /* Custom scrollbar for modal */
        .modal-body-custom::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-body-custom::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .modal-body-custom::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .modal-body-custom::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <button class="refresh-btn" onclick="window.location.reload()">
                <i class="ri-refresh-line"></i>Refresh
            </button>
            <h1 class="dashboard-title"><i class="ri-building-2-line dashboard-title-icon"></i>centers summary</h1>
            <p class="dashboard-subtitle">{{ date('d/m/Y, H:i:s A') }}</p>
        </div>

         <!-- Branch Cards (similar to ho-dashboard) -->
         <div class="branch-grid">
             @php
                 $colors = ['purple', 'pink', 'blue', 'orange', 'green'];
                 $index = 0;
                 // Group data by branch
                 $branchData = collect($userData)->groupBy('branch_name')->map(function($centers, $branchName) {
                     return [
                         'name' => $branchName ?? 'Unknown Branch',
                         'centers' => $centers->count(),
                         'routes' => $centers->pluck('name')->unique()->count(),
                         'groups' => $centers->sum('Groups'),
                         'members' => $centers->sum('Members')
                     ];
                 });
             @endphp
             
             @foreach($branchData as $b)
                 @php
                     // Find branch ID for switching
                     $branchId = collect($userData)->where('branch_name', $b['name'])->first()->branch_id ?? null;
                 @endphp
                 <div class="branch-card {{ $colors[$index % count($colors)] }}" data-branch="{{ $branchId }}" title="Click to view detailed hierarchy of {{ $b['name'] }} branch" style="height: 320px !important; overflow: visible !important; position: relative; cursor: pointer;">
                     <div class="card-inner" style="transform: none !important; transition: none !important;">
                         <!-- Front of Card -->
                         <div class="card-front" style="position: static !important; display: flex !important; flex-direction: column !important; height: 320px !important; backface-visibility: visible !important;">
                             <div class="branch-header">
                                 <h3 class="branch-name">{{ $b['name'] }}</h3>
                                 <div class="branch-icon"><i class="ri-building-line"></i></div>
                             </div>
                             
                             <div class="metrics-grid" style="display: grid !important; grid-template-columns: 1fr 1fr !important; grid-template-rows: 1fr 1fr !important; gap: 12px !important; flex: 1 !important; margin-bottom: 16px !important; padding: 12px !important;">
                                 <div class="metric-card" style="background: rgba(255, 255, 255, 0.2) !important; border-radius: 10px !important; padding: 12px !important; text-align: center !important; display: flex !important; flex-direction: column !important; justify-content: center !important; min-height: 70px !important; backdrop-filter: blur(5px) !important;">
                                     <div class="metric-label">Routes</div>
                                     <div class="metric-value">{{ number_format($b['routes']) }}</div>
                                 </div>
                                 <div class="metric-card" style="background: rgba(255, 255, 255, 0.2) !important; border-radius: 10px !important; padding: 12px !important; text-align: center !important; display: flex !important; flex-direction: column !important; justify-content: center !important; min-height: 70px !important; backdrop-filter: blur(5px) !important;">
                                     <div class="metric-label">Centers</div>
                                     <div class="metric-value">{{ number_format($b['centers']) }}</div>
                                 </div>
                                 <div class="metric-card" style="background: rgba(255, 255, 255, 0.2) !important; border-radius: 10px !important; padding: 12px !important; text-align: center !important; display: flex !important; flex-direction: column !important; justify-content: center !important; min-height: 70px !important; backdrop-filter: blur(5px) !important;">
                                     <div class="metric-label">Groups</div>
                                     <div class="metric-value">{{ number_format($b['groups']) }}</div>
                                 </div>
                                 <div class="metric-card" style="background: rgba(255, 255, 255, 0.2) !important; border-radius: 10px !important; padding: 12px !important; text-align: center !important; display: flex !important; flex-direction: column !important; justify-content: center !important; min-height: 70px !important; backdrop-filter: blur(5px) !important;">
                                     <div class="metric-label">Members</div>
                                     <div class="metric-value">{{ number_format($b['members']) }}</div>
                                 </div>
                             </div>
                         </div>
                         
                     </div>
                     
                 </div>
                 @php $index++; @endphp
             @endforeach
         </div>

         <!-- Summary Cards -->
         <div class="summary-cards">
             @php
                 $totalRoutes = count($route);
                 $totalCenters = count($userData);
                 $totalGroups = collect($userData)->sum('Groups');
                 $totalMembers = collect($userData)->sum('Members');
                 $branchNames = collect($userData)->pluck('branch_name')->filter()->unique();
             @endphp
             
             <div class="summary-card blue">
                 <div class="summary-label">Total Routes</div>
                 <div class="summary-value">{{ number_format($totalRoutes) }}</div>
             </div>
             
             <div class="summary-card green">
                 <div class="summary-label">Total Centers</div>
                 <div class="summary-value">{{ number_format($totalCenters) }}</div>
             </div>
             
             <div class="summary-card orange">
                 <div class="summary-label">Total Groups</div>
                 <div class="summary-value">{{ number_format($totalGroups) }}</div>
             </div>
             
             <div class="summary-card teal">
                 <div class="summary-label">Total Members</div>
                 <div class="summary-value">{{ number_format($totalMembers) }}</div>
             </div>
             
             <div class="summary-card purple">
                 <div class="summary-label">Active Branches</div>
                 <div class="summary-value">{{ number_format(count($branchNames)) }}</div>
             </div>
         </div>

        <!-- Full Page Branch Details Modal -->
        <div class="branch-modal-overlay" id="branchModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.75); z-index: 2000; backdrop-filter: blur(12px);">
            <div class="branch-modal" style="position: relative; width: 92%; max-width: 1400px; height: 92%; margin: 2% auto; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e2e8f0;">
                
                <!-- Modal Header -->
                <div class="modal-header-custom" style="background: #ffffff; color: #1e293b; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; position: relative;">
                    <div>
                        <h2 class="mb-1" id="modalBranchName" style="font-size: 1.75rem; font-weight: 600; margin: 0; color: #0f172a; letter-spacing: -0.025em;">Branch Details</h2>
                        <p class="mb-0" style="color: #64748b; font-size: 0.875rem; font-weight: 400; margin-top: 4px;">Complete organizational hierarchy and structure</p>
                    </div>
                    <button class="btn p-0" onclick="closeBranchModal()" style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; cursor: pointer;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                        <i class="ri-close-line" style="font-size: 1.25rem;"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body-custom" style="flex: 1; padding: 32px; overflow-y: auto; background: #fafafa;">
                    <div id="modalContent">
                        <div class="text-center py-5">
                            <div class="spinner-border" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px; color: #3b82f6;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-4" style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Loading branch details...</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        // make initials safe even if name is missing
        function computeInitials(name) {
            if (!name || typeof name !== 'string') return '?';
            const parts = name.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 0) return '?';
            const initials = parts.slice(0, 2).map(p => p.charAt(0)).join('');
            return initials.toUpperCase();
        }

        // Branch modal functionality
        document.querySelectorAll('.branch-card').forEach(card => {
            card.addEventListener('click', (e) => {
                e.stopPropagation();
                const branchId = card.getAttribute('data-branch');
                const branchName = card.querySelector('.branch-name').textContent;
                
                if(!branchId) return;
                
                // Show modal
                showBranchModal(branchId, branchName);
            });
        });
        
        // Close modal when clicking on overlay
        document.getElementById('branchModal').addEventListener('click', (e) => {
            if (e.target.classList.contains('branch-modal-overlay')) {
                closeBranchModal();
            }
        });
        
        // Keyboard support - ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.getElementById('branchModal').style.display !== 'none') {
                closeBranchModal();
            }
        });
        
        function showBranchModal(branchId, branchName) {
            const modal = document.getElementById('branchModal');
            document.getElementById('modalBranchName').textContent = `${branchName} Branch Details`;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scroll
            
            // Load branch data
            loadBranchDetails(branchId);
        }
        
        function closeBranchModal() {
            const modal = document.getElementById('branchModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore background scroll
        }
        
        function loadBranchDetails(branchId) {
            const contentDiv = document.getElementById('modalContent');
            
            // Show loading spinner
            contentDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px; color: #3b82f6;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-4" style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Loading branch details...</p>
                </div>
            `;
            
            // Fetch branch hierarchy data (GET does not need CSRF)
            fetch(`/api/branch-hierarchy/${branchId}`, { method: 'GET' })
            .then(response => {
                if (!response.ok) throw new Error('Failed to load data');
                return response.json();
            })
            .then(data => {
                renderHierarchy(contentDiv, data);
            })
            .catch(error => {
                contentDiv.innerHTML = `
                    <div class="text-center py-5" style="padding: 4rem 2rem;">
                        <div>
                            <i class="ri-error-warning-line" style="font-size: 3.5rem; color: #ef4444;"></i>
                            <h4 class="mt-4 mb-2" style="color: #dc2626; font-weight: 600; font-size: 1.25rem;">Failed to load branch details</h4>
                            <p class="mb-4" style="color: #64748b; font-size: 0.95rem; max-width: 400px; margin: 0 auto 2rem auto;">Please check your connection and try again</p>
                            <button class="btn" onclick="loadBranchDetails(${branchId})" style="background: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; cursor: pointer;" onmouseover="this.style.background='#2563eb';" onmouseout="this.style.background='#3b82f6';">
                                <i class="ri-refresh-line"></i> Retry
                            </button>
                        </div>
                    </div>
                `;
            });
        }
        
        function renderHierarchy(container, data) {
            if (!data.routes || data.routes.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5" style="padding: 4rem 2rem;">
                        <i class="ri-inbox-line" style="font-size: 3.5rem; color: #cbd5e1;"></i>
                        <h4 class="mt-4 mb-2" style="color: #64748b; font-weight: 600; font-size: 1.25rem;">No data available</h4>
                        <p style="color: #94a3b8; font-size: 0.95rem; max-width: 400px; margin: 0 auto;">This branch doesn't have any routes configured yet.</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="hierarchy-container" style="display: flex; flex-direction: column; gap: 24px;">';
            
            data.routes.forEach((route, routeIndex) => {
                // Calculate total customers in this route
                let totalCustomers = 0;
                if (route.centers) {
                    route.centers.forEach(center => {
                        if (center.groups) {
                            center.groups.forEach(group => {
                                totalCustomers += group.customer_count || 0;
                            });
                        }
                    });
                }
                
                html += `
                    <div class="route-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.06);">
                        <!-- Route Header -->
                        <div class="route-header" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 20px 28px; border-bottom: 1px solid #e2e8f0; cursor: pointer;" onclick="toggleSection('route-${route.id}')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="route-icon" style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.25);">
                                        <i class="ri-route-line" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 style="color: #0f172a; font-weight: 700; font-size: 1.25rem; margin: 0; letter-spacing: -0.025em;">${route.name}</h4>
                                        <p style="color: #64748b; font-size: 0.875rem; margin: 2px 0 0 0;">Route Management</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stats-badge" style="background: #eff6ff; border: 1px solid #dbeafe; color: #1e40af; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                                        ${totalCustomers} customers
                                    </div>
                                    <div class="expand-btn" style="width: 40px; height: 40px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                        <i class="ri-arrow-down-s-line" id="arrow-route-${route.id}" style="font-size: 1.25rem; color: #64748b; transition: transform 0.2s ease;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Route Content -->
                        <div class="route-content collapse-content" id="route-${route.id}" style="display: none; padding: 20px 28px 24px 28px;">
                `;
                
                if (route.centers && route.centers.length > 0) {
                    route.centers.forEach(center => {
                        // Calculate total customers in this center
                        let centerCustomers = 0;
                        if (center.groups) {
                            center.groups.forEach(group => {
                                centerCustomers += group.customer_count || 0;
                            });
                        }
                        
                        html += `
                            <div class="center-card" style="margin: 0 0 16px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                                <div class="center-header" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); padding: 20px 28px; border-bottom: 1px solid #d1fae5; cursor: pointer;" onclick="toggleSection('center-${center.id}')">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="center-icon" style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.25);">
                                                <i class="ri-building-2-line" style="color: white; font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h4 style="color: #065f46; font-weight: 700; font-size: 1.25rem; margin: 0; letter-spacing: -0.025em;">${center.name}</h4>
                                                <p style="color: #64748b; font-size: 0.875rem; margin: 2px 0 0 0;">Center Management</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="stats-badge" style="background: #dcfce7; border: 1px solid #bbf7d0; color: #16a34a; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                                                ${centerCustomers} customers
                                            </div>
                                            <div class="expand-btn" style="width: 40px; height: 40px; background: #ffffff; border: 2px solid #d1fae5; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                                <i class="ri-arrow-down-s-line" id="arrow-center-${center.id}" style="font-size: 1.25rem; color: #16a34a; transition: transform 0.2s ease;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="center-content collapse-content" id="center-${center.id}" style="display: none; padding: 20px;">
                        `;
                        
                        if (center.groups && center.groups.length > 0) {
                            center.groups.forEach(group => {
                                html += `
                                    <div class="group-card" style="margin-bottom: 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                                        <div class="group-header" style="background: linear-gradient(135deg, #f4f3ff 0%, #ede9fe 100%); padding: 12px 16px; border-bottom: 1px solid #d8b4fe; cursor: pointer;" onclick="toggleSection('group-${group.id}')">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="group-icon" style="width: 36px; height: 36px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.25);">
                                                        <i class="ri-group-line" style="color: white; font-size: 1.1rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 style="color: #581c87; font-weight: 600; font-size: 0.95rem; margin: 0;">${group.name}</h6>
                                                        <p style="color: #6b7280; font-size: 0.75rem; margin: 2px 0 0 0;">Group Management</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="stats-badge" style="background: #f3e8ff; border: 1px solid #d8b4fe; color: #7c2d92; padding: 4px 8px; border-radius: 5px; font-weight: 600; font-size: 0.75rem;">
                                                        ${group.customer_count} members
                                                    </div>
                                                    <div class="expand-btn" style="width: 28px; height: 28px; background: #ffffff; border: 2px solid #d8b4fe; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="ri-arrow-down-s-line" id="arrow-group-${group.id}" style="font-size: 0.9rem; color: #8b5cf6; transition: transform 0.2s ease;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="group-content collapse-content" id="group-${group.id}" style="display: none; padding: 12px;">
                                            <div class="customers-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 8px;">
                                `;
                                
                                if (group.customers && group.customers.length > 0) {
                                    group.customers.forEach(customer => {
                                        html += `
                                            <div class="customer-item" style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 12px; transition: all 0.2s ease;">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-avatar" style="width: 42px; height: 42px; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; box-shadow: 0 2px 4px rgba(107, 114, 128, 0.15);">
                                                        <span style="color: white; font-weight: 700; font-size: 0.8rem;">${computeInitials(customer.name)}</span>
                                                    </div>
                                                    <div class="customer-info" style="flex: 1; min-width: 0;">
                                                        <div style="color: #111827; font-weight: 600; font-size: 0.875rem; line-height: 1.2; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${customer.name}</div>
                                                        <div style="color: #6b7280; font-size: 0.75rem; font-weight: 500; line-height: 1; display: flex; align-items: center;">
                                                            <i class="ri-user-line" style="font-size: 0.7rem; margin-right: 4px;"></i>
                                                            ${customer.cus_number}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    html += '<div class="empty-state" style="text-align: center; padding: 24px; color: #9ca3af;">No customers in this group</div>';
                                }
                                
                                html += `
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html += '<div class="empty-state" style="text-align: center; padding: 20px; color: #9ca3af; font-size: 0.9rem;">No groups in this center</div>';
                        }
                        
                        html += `
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += '<div class="empty-state" style="text-align: center; padding: 32px; color: #9ca3af; font-size: 1rem;">No centers in this route</div>';
                }
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            container.innerHTML = html;
        }
        
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const arrow = document.getElementById(`arrow-${sectionId}`);
            
            if (section.style.display === 'none' || !section.style.display) {
                section.style.display = 'block';
                arrow.className = 'ri-arrow-up-s-line';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                section.style.display = 'none';
                arrow.className = 'ri-arrow-down-s-line';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

    </script>
@endsection