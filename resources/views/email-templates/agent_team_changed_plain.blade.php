Hello,

{{ trim($agent->first_name . ' ' . ($agent->last_name ?? '')) }} (Application ID: {{ $agent->reg_application_id ?? 'N/A' }}) has been 
{{ $action === 'assigned' ? 'assigned to' : 'removed from' }} the Recovery Team.

Action performed by: {{ $performedByName }} ({{ $roleName }})
Application ID: {{ $agent->reg_application_id }}

City: {{ $agent->current_city->city_name ?? 'N/A' }}
Zone: {{ $agent->zone->name ?? 'N/A' }}

{{ $footerContent }}

--
GreenDriveConnect Team
