<?php

return <<< 'EOD'
# Disable indexation
Options -Indexes

# Prevent the site from being embedded in a frame
Header set X-Frame-Options "DENY"

# Prevent browsers from sniffing files
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
</IfModule>

# redirection
Options +FollowSymlinks
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ public/index.php [L]
EOD;
