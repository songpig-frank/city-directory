<?php
/**
 * CityDirectory — Email Helper
 * Uses PHP mail() for InterServer shared hosting compatibility.
 */

/**
 * Send an email using PHP's built-in mail() function.
 */
function send_email(string $to, string $subject, string $body, bool $is_html = true): bool {
    $from_email = config('from_email') ?? ('noreply@' . config('domain'));
    $from_name = config('from_name') ?? config('site_name');

    $headers = [
        'From'         => "{$from_name} <{$from_email}>",
        'Reply-To'     => config('admin_email') ?? $from_email,
        'MIME-Version' => '1.0',
        'X-Mailer'     => 'CityDirectory/1.0',
    ];

    if ($is_html) {
        $headers['Content-Type'] = 'text/html; charset=UTF-8';
        $body = email_template($subject, $body);
    } else {
        $headers['Content-Type'] = 'text/plain; charset=UTF-8';
    }

    $header_str = '';
    foreach ($headers as $key => $value) {
        $header_str .= "{$key}: {$value}\r\n";
    }

    return @mail($to, $subject, $body, $header_str);
}

/**
 * Wrap email body in a simple HTML template.
 */
function email_template(string $title, string $body): string {
    $site_name = clean(config('site_name'));
    $base_url = config('base_url');

    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"></head>
    <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">
        <div style="text-align: center; padding: 20px 0; border-bottom: 2px solid #4ECDC4;">
            <h2 style="margin: 0; color: #2C3E50;">{$site_name}</h2>
        </div>
        <div style="padding: 30px 0;">
            <h3 style="color: #2C3E50;">{$title}</h3>
            {$body}
        </div>
        <div style="padding: 20px 0; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999;">
            <p>This email was sent from <a href="{$base_url}" style="color: #4ECDC4;">{$site_name}</a></p>
            <p>If you did not expect this email, you can safely ignore it.</p>
        </div>
    </body>
    </html>
    HTML;
}

/**
 * Send listing expiry reminder.
 */
function send_expiry_reminder(array $listing, int $days_remaining): bool {
    if (empty($listing['email'])) return false;

    $renewal_link = base_url("renew/{$listing['renewal_token']}");
    $body = "<p>Your listing <strong>{$listing['name']}</strong> will expire in <strong>{$days_remaining} days</strong>.</p>
             <p>To keep your listing active, please renew it by clicking the button below:</p>
             <p style='text-align: center; padding: 20px;'>
                <a href='{$renewal_link}' style='background: #4ECDC4; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Renew My Listing</a>
             </p>
             <p style='font-size: 13px; color: #666;'>If you no longer need this listing, no action is required — it will be automatically removed after expiry.</p>";

    return send_email(
        $listing['email'],
        "Your listing expires in {$days_remaining} days — " . config('site_name'),
        $body
    );
}

/**
 * Send notification to admin about new listing submission.
 */
function send_new_listing_notification(array $listing): bool {
    $admin_url = base_url("admin/listings?status=pending");
    $listing_name = $listing['name'];
    $listing_type = $listing['type'];
    $cat_name = $listing['category_name'] ?? 'N/A';
    $body = "<p>A new listing has been submitted and is awaiting your review:</p>
             <ul>
                <li><strong>Name:</strong> {$listing_name}</li>
                <li><strong>Category:</strong> {$cat_name}</li>
                <li><strong>Type:</strong> {$listing_type}</li>
             </ul>
             <p style='text-align: center; padding: 20px;'>
                <a href='{$admin_url}' style='background: #FF6B35; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Review Listing</a>
             </p>";

    return send_email(
        config('admin_email'),
        "New listing submitted: {$listing['name']}",
        $body
    );
}
