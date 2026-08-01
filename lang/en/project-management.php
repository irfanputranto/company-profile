<?php

$indonesian = require __DIR__.'/../id/project-management.php';

return array_replace_recursive($indonesian, [
    'navigation' => ['title' => 'Project Management', 'projects' => 'Client Projects', 'companies' => 'Client Companies'],
    'board' => [
        'title' => 'Project Board', 'open' => 'Open Board', 'description' => 'Move feature cards between columns to update their status like Trello.',
        'back_to_detail' => 'Project Detail', 'drag_hint' => 'Drag a card to another column or use the status selector on touch devices.',
        'empty' => 'No cards in this column.', 'moved' => 'Card status updated.',
        'move_failed' => 'The card could not be moved. Please try again.', 'cards' => ':count cards',
    ],
    'companies' => ['title' => 'Client Companies', 'description' => 'Manage client organizations and contacts. One company may own multiple projects.', 'add' => 'Add company', 'search' => 'Search company or contact...', 'empty' => 'No client companies yet.', 'create_title' => 'Add Client Company', 'edit_title' => 'Edit Client Company'],
    'projects' => ['title' => 'Client Projects', 'description' => 'Track scope, phases, features, documents, technologies, costs, and servers for each project.', 'add' => 'Add project', 'search' => 'Search project name or code...', 'empty' => 'No client projects yet.', 'create_title' => 'Add Client Project', 'create_description' => 'Record the initial contract, schedule, and cost information.', 'edit_title' => 'Edit Client Project', 'edit' => 'Edit project'],
    'phases' => ['title' => 'Phases and Progress', 'short' => 'phases', 'description' => 'Break work into phases so developers understand completed and upcoming deliverables.', 'add' => 'Add phase', 'edit' => 'Edit phase', 'delete' => 'Delete phase', 'empty' => 'No project phases yet.'],
    'features' => ['title' => 'Phase features', 'add' => 'Add feature', 'edit' => 'Edit feature', 'empty' => 'No features in this phase.'],
    'documents' => ['title' => 'Project Documents', 'short' => 'documents', 'description' => 'Contracts, syllabus, requirements, designs, reports, and supporting files.', 'upload' => 'Upload documents', 'empty' => 'No documents yet.'],
    'technologies' => ['title' => 'Technologies', 'description' => 'Technology stack and versions used by the project.', 'add' => 'Add technology', 'empty' => 'No technologies yet.'],
    'servers' => ['title' => 'Servers and Infrastructure', 'description' => 'Manage access, costs, margin, and server expiry.', 'add' => 'Add server', 'edit' => 'Edit server', 'credentials' => 'Server credentials', 'credentials_warning' => 'This information is confidential. Do not share or copy it to an insecure location.', 'empty' => 'No servers yet.'],
    'notifications' => ['title' => 'Server Reminders', 'read_all' => 'Mark all as read', 'empty' => 'No new reminders.', 'expires' => 'Expires :date'],
    'fields' => [
        'company_name' => 'Company name', 'contact_person' => 'Primary contact', 'phone' => 'Phone', 'address' => 'Address', 'notes' => 'Notes', 'projects' => 'Projects',
        'company' => 'Company', 'select_company' => 'Select company', 'all_companies' => 'All companies', 'code' => 'Project code', 'project_name' => 'Project name',
        'project' => 'Project', 'scope' => 'Project Scope / Syllabus', 'scope_hint' => 'Describe goals, scope, constraints, and key outcomes.', 'all_status' => 'All statuses',
        'currency' => 'Currency', 'started_at' => 'Start date', 'due_at' => 'Due date', 'completed_at' => 'Completion date', 'timeline' => 'Timeline', 'until' => 'until',
        'original_cost' => 'Original cost', 'project_price' => 'Project price', 'sell_price' => 'Selling price', 'cost' => 'Cost:', 'profit' => 'Profit',
        'margin_hint' => 'Profit percentage is calculated automatically from price and cost.', 'progress' => 'Overall progress', 'duration' => 'Project duration', 'data' => 'Data',
        'phase_name' => 'Phase name, e.g. Phase 1 — MVP', 'phase_scope' => 'Phase scope', 'deliverables' => 'Phase deliverables', 'feature_name' => 'Feature name',
        'description' => 'Description', 'acceptance_criteria' => 'Acceptance criteria', 'server_name' => 'Server name', 'username' => 'Username', 'password' => 'Password',
        'credential_notes' => 'Credential notes', 'secret_unchanged' => 'Leave blank to keep the current secret.', 'billing_cycle' => 'Billing cycle',
        'purchased_at' => 'Purchase date', 'expires_at' => 'Expiry date', 'reminder_days' => 'Remind before (days)',
    ],
    'status' => [
        'projects' => ['planning' => 'Planning', 'in_progress' => 'In progress', 'on_hold' => 'On hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
        'phases' => ['pending' => 'Pending', 'in_progress' => 'In progress', 'review' => 'Review', 'completed' => 'Completed', 'blocked' => 'Blocked'],
        'features' => ['backlog' => 'Backlog', 'in_progress' => 'In progress', 'review' => 'Review', 'done' => 'Done', 'blocked' => 'Blocked'],
        'servers' => ['active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'],
    ],
    'categories' => ['documents' => ['contract' => 'Contract', 'syllabus' => 'Syllabus', 'requirement' => 'Requirement', 'design' => 'Design', 'report' => 'Report', 'invoice' => 'Invoice', 'other' => 'Other']],
    'billing_cycles' => ['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly', 'one_time' => 'One-time'],
]);
