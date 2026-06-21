<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$event_id = intval($_GET['id']);

// SQL to fetch event details and the organizer (creator) name
$sql = "SELECT e.*, c.categoryName, u.name AS organizer_name 
        FROM events e 
        JOIN event_category c ON e.category_id = c.category_id 
        JOIN users u ON e.created_by = u.user_id 
        WHERE e.event_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    die("Event not found.");
}

if ($event['visibility'] === 'University' && !isset($_SESSION['user_id'])) {
    die("<script>alert('This event is for university members only. Please login.'); window.location='../auth/login.php';</script>");
}

// Check registration status (closed / full / already registered)
$isClosed = false;
$isFull = false;
$isRegistered = false;

$today = date("Y-m-d");

// closed
if (!empty($event['registration_close_date']) && $today > $event['registration_close_date']) {
    $isClosed = true;
}

// full (max_participants = 0 means unlimited)
$currentCount = 0;
$countSql = "SELECT COUNT(*) AS total FROM registrations WHERE event_id = ?";
$stmtCount = $conn->prepare($countSql);
$stmtCount->bind_param("i", $event_id);
$stmtCount->execute();
$currentCount = $stmtCount->get_result()->fetch_assoc()['total'] ?? 0;
$stmtCount->close();

if ($event['max_participants'] > 0 && $currentCount >= $event['max_participants']) {
    $isFull = true;
}

// already registered
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $checkSql = "SELECT registration_id FROM registrations WHERE event_id = ? AND user_id = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("ii", $event_id, $user_id);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result();
    $isRegistered = ($res->num_rows > 0);
    $stmtCheck->close();
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($event['event_name']) ?> - Details</title>
    <link rel="stylesheet" href="<?= BASE_PATH_CSS ?>style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* matching the Event Management Hub Glass-morphism */
        .detail-glass-container {
            background: rgba(2, 37, 61, 0.85); /* Matches Dashboard Navy transparency */
            border: 2px solid #2c8ca0;
            border-radius: 15px;
            padding: 40px;
            margin: -80px auto 40px auto; /* Overlaps hero banner like your Hub */
            max-width: 1000px;
            position: relative;
            z-index: 5;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        .poster-frame {
            border: 1px solid #2c8ca0;
            padding: 10px;
            border-radius: 10px;
            background: rgba(0, 26, 44, 0.5);
        }

        .event-info h1 {
            color: #4fd99d; /* SOE Teal */
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .info-label {
            color: #4fd99d;
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .description-box {
            background: rgba(0, 26, 44, 0.6);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4fd99d;
            line-height: 1.6;
        }

        .schedule-venue {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            color: #e0e0e0;
        }

        .schedule-venue i {
            color: #4fd99d;
            margin-right: 8px;
        }

        hr {
            border: 0;
            border-top: 1px solid rgba(44, 140, 160, 0.3);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include '../include/topNav.php'; ?>
    
    <section class="hero" style="height: 50vh;">
        <div class="overlay"></div>
        <div class="hero-content">
        <h1>EVENT INFORMATION</h1>
        <p>Discover details about your selected activity</p>
        </div>
    </section>

    <main>
        <div class="detail-glass-container">
        <div style="display: flex; gap: 40px; flex-wrap: wrap; align-items: flex-start;">    
        <div style="flex: 1; min-width: 300px;" class="poster-frame">
    <?php
      $posters = [];
      if (!empty($event['poster_path']))  $posters[] = $event['poster_path'];
      if (!empty($event['poster2_path'])) $posters[] = $event['poster2_path'];
      if (!empty($event['poster3_path'])) $posters[] = $event['poster3_path'];
      if (!empty($event['poster4_path'])) $posters[] = $event['poster4_path'];
    ?>

    <?php if (!empty($posters)): ?>
        <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($posters[0]) ?>"
            alt="Poster" style="width: 100%; border-radius: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">

        <?php if (count($posters) > 1): ?>
            <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
                <?php foreach ($posters as $p): ?>
                    <img src="<?= BASE_PATH_UPLOADS . htmlspecialchars($p) ?>"
                        alt="Poster"
                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border:1px solid rgba(44, 140, 160, 0.5);">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p style="color:#fff;">No poster available.</p>
    <?php endif; ?>
</div>


                <div style="flex: 1.5; min-width: 300px;" class="event-info">
                    <h1><?= htmlspecialchars($event['event_name']) ?></h1>
                    
                    <p><span class="info-label">Category:</span> <?= $event['categoryName'] ?></p>
                    <p><span class="info-label">Organizer:</span> <?= htmlspecialchars($event['organizer_name']) ?></p>
                    <p><span class="info-label">Fee:</span> <?= ($event['fee'] > 0) ? "RM " . number_format($event['fee'], 2) : "Free" ?></p>
                    <p><span class="info-label">Visibility:</span> <?= htmlspecialchars($event['visibility']) ?></p>
                    <p><span class="info-label">Contact:</span>
                    <?= htmlspecialchars($event['contact_person']) ?> (<?= htmlspecialchars($event['contact_number']) ?>)</p>
                    <p><span class="info-label">Reg Close:</span> <?= !empty($event['registration_close_date']) ? date('d M Y', strtotime($event['registration_close_date'])) : "-" ?></p>

                    <hr>
                    
                    <h3><i class="fas fa-info-circle" style="color: #4fd99d;"></i> Description</h3>
                    <div class="description-box"> <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </div>
                    
                    <h3><i class="fas fa-map-marked-alt" style="color: #4fd99d;"></i> Logistics</h3>
                    <div class="schedule-venue">
                    <span>
                    <i class="fas fa-calendar"></i>
                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                    <?= !empty($event['event_time']) ? " | " . date('h:i A', strtotime($event['event_time'])) : "" ?>
                    </span>
                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['venue']) ?> (<?= htmlspecialchars($event['mode']) ?>)</span>
                    </div>

                    <p style="margin-top:15px; color:#e0e0e0;">
                    <span class="info-label">Max:</span>
                    <?php if ($event['max_participants'] == 0): ?>
                    Unlimited (<?= $currentCount ?> registered)
                    <?php else: ?>
                    <?= $currentCount ?> / <?= intval($event['max_participants']) ?> registered
                    <?php endif; ?> </p>
                    <?php if (!empty($event['remarks'])): ?>
                    <p style="margin-top:15px;">
                    <span class="info-label">Remarks:</span>
                    <?= nl2br(htmlspecialchars($event['remarks'])) ?>
                    </p>
                    <?php endif; ?>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($isRegistered): ?>
                    <button class="btn" style="flex: 1; text-align: center; opacity:0.6; cursor:not-allowed;" disabled>
                    Already Registered
                    </button>

                    <?php elseif ($isClosed): ?>
                    <button class="btn" style="flex: 1; text-align: center; opacity:0.6; cursor:not-allowed;" disabled>
                    Registration Closed
                    </button>

                    <?php elseif ($isFull): ?>
                    <button class="btn" style="flex: 1; text-align: center; opacity:0.6; cursor:not-allowed;" disabled>
                    Event Full
                    </button>

                    <?php else: ?>
                    <a href="../auth/register_event_action.php?id=<?= $event['event_id'] ?>"
                    class="btn" style="flex: 1; text-align: center;"
                    onclick="return confirm('Join this event?')">
                    Register Now
                    </a>
                    <?php endif; ?>

                    <?php else: ?>
                    <a href="../auth/login.php" class="btn" style="flex: 1; text-align: center;">
                    Login to Register
                    </a>
                    <?php endif; ?>   
                        <a href="../index.php" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid #2c8ca0; flex: 1; text-align: center;">
                            Back to Listing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

 <?php include '../include/footer.php'; ?>
</body>
</html>