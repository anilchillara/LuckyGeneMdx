<?php
/**
 * print-barcode.php
 * Admin page to render and print barcode labels for kits.
 * Accepts either a single `kit_id` or an `order_id` (to print all kits for an order).
 */
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

$db = Database::getInstance()->getConnection();

$kit_id = isset($_GET['kit_id']) ? (int)$_GET['kit_id'] : 0;
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$kits = [];

if ($kit_id > 0) {
    $stmt = $db->prepare("
        SELECT k.kit_id, k.kit_barcode, k.is_gift, o.order_number 
        FROM kits k
        JOIN orders o ON k.order_id = o.order_id
        WHERE k.kit_id = :kit_id
    ");
    $stmt->execute([':kit_id' => $kit_id]);
    $kits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($order_id > 0) {
    $stmt = $db->prepare("
        SELECT k.kit_id, k.kit_barcode, k.is_gift, o.order_number 
        FROM kits k
        JOIN orders o ON k.order_id = o.order_id
        WHERE k.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $order_id]);
    $kits = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($kits)) {
    die("No kit data found to print.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Barcodes</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        /* Reset and print-specific styles */
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: 'Inter', sans-serif;
            color: #000;
        }
        
        .label-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* Standard Dymo 2x1 inch label dimensions approximately */
            width: 2in;
            height: 1in;
            margin: 0 auto;
            page-break-after: always;
            box-sizing: border-box;
            padding: 0.1in;
        }

        .label-page:last-child {
            page-break-after: auto;
        }
        
        .barcode-img {
            max-width: 100%;
            height: auto;
        }
        
        .label-meta {
            font-size: 0.35in; /* Adjusted for Dymo */
            text-align: center;
            margin-top: -5px; /* Pull text closer to barcode */
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: monospace;
            font-weight: bold;
        }
        
        .order-ref {
            font-size: 0.15in;
            color: #333;
        }

        .gift-indicator {
            font-size: 0.15in;
            font-weight: bold;
            display: inline-block;
            border: 1px solid #000;
            padding: 1px 4px;
            border-radius: 3px;
        }
        
        /* Screen preview styles */
        @media screen {
            body {
                background: #f4f5f7;
                padding: 2rem;
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                justify-content: center;
            }
            .label-page {
                background: #fff;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                border: 1px dashed #ccc;
                width: 3in;
                height: 1.5in;
                transform: scale(1.5);
                margin-bottom: 1in;
            }
            .print-btn-container {
                width: 100%;
                text-align: center;
                margin-bottom: 2rem;
            }
            .print-btn {
                padding: 10px 20px;
                background: #00b3a4;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                font-weight: bold;
            }
        }
        
        /* Hide buttons during print */
        @media print {
            .print-btn-container {
                display: none;
            }
            @page {
                margin: 0; /* Remove default browser margins */
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">🖨️ Print Labels</button>
    </div>

    <?php foreach ($kits as $kit): ?>
        <div class="label-page">
            <svg class="barcode" 
                 jsbarcode-format="CODE128" 
                 jsbarcode-value="<?php echo htmlspecialchars($kit['kit_barcode']); ?>" 
                 jsbarcode-textmargin="0" 
                 jsbarcode-fontoptions="bold"
                 jsbarcode-width="2"
                 jsbarcode-height="40"
                 jsbarcode-displayvalue="true"
                 jsbarcode-fontsize="14">
            </svg>
            <div class="label-meta">
                <span class="order-ref">#<?php echo htmlspecialchars($kit['order_number']); ?></span>
                <?php if ($kit['is_gift']): ?>
                    <span class="gift-indicator">🎁 GIFT</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        // Initialize all JsBarcodes
        JsBarcode(".barcode").init();
        
        // Auto-print prompt
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
