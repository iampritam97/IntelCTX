<?php
// admin/insert_submission.php
// Handles intel submission: EMAIL ONLY, no DB insert

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Grab fields safely
    $category   = trim($_POST['category']   ?? '');
    $entity     = trim($_POST['entity_name'] ?? '');
    $aliases    = trim($_POST['aliases']    ?? '');
    $narrative  = trim($_POST['narrative']  ?? '');
    $ioc        = trim($_POST['ioc']        ?? '');
    $detection  = trim($_POST['detection']  ?? '');
    $reference  = trim($_POST['reference']  ?? '');

    // 🔴 CHANGE THIS to your personal email
    $to = "pritamdash1997@gmail.com";

    $subject = "[APT Intel] New Intel Submission: {$category} — {$entity}";

    // Build plain-text body
    $body =
"New threat intelligence submission received:

Category: {$category}
Entity Name: {$entity}
Aliases / Tags: {$aliases}

Narrative / Notable Attacks:
{$narrative}

Indicators of Compromise:
{$ioc}

Detection / Defensive Guidance:
{$detection}

References / Source:
{$reference}

Submitted at: " . date('Y-m-d H:i:s') . "
";

    // Basic headers
    $headers = "From: APT Intel <no-reply@your-domain.com>\r\n" .
               "Reply-To: no-reply@your-domain.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Send email
    $sent = @mail($to, $subject, $body, $headers);

    if ($sent) {
        // Simple success page/redirect
        header("Location: ../submit_success.php");
        exit;
    } else {
        // If mail() fails, show basic error
        echo "<p>Unable to send email. Please check your mail server configuration.</p>";
        echo "<p><a href=\"../submit_intel.php\">Back to Submit Intel</a></p>";
        exit;
    }

} else {
    // Direct access without POST
    header("Location: ../submit_intel.php");
    exit;
}
