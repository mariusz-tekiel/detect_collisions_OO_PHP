<?php
declare(strict_types=1);

use App\Geometry\{Point, Circle, Rectangle, Triangle};
use App\Collision\CollisionService;

require dirname(__DIR__, 1) . '/../src/autoload.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = $_POST ?: json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    $case = $input['case'] ?? null;
    if (!$case) throw new InvalidArgumentException('Missing case');

    switch ($case) {
        case 'point-point':
            $a = new Point((float)$input['x1'], (float)$input['y1'], 'A');
            $b = new Point((float)$input['x2'], (float)$input['y2'], 'B');
            $hit = CollisionService::pointPoint($a,$b);
            break;
        case 'point-circle':
            $a = new Point((float)$input['px'], (float)$input['py'], 'P');
            $b = new Circle((float)$input['cx'], (float)$input['cy'], (float)$input['r'], 'C');
            $hit = CollisionService::pointCircle($a,$b);
            break;
        case 'circle-circle':
            $a = new Circle((float)$input['c1x'], (float)$input['c1y'], (float)$input['r1'], 'C1');
            $b = new Circle((float)$input['c2x'], (float)$input['c2y'], (float)$input['r2'], 'C2');
            $hit = CollisionService::circleCircle($a,$b);
            break;
        case 'point-rect':
            $a = new Point((float)$input['px'], (float)$input['py'], 'P');
            $b = new Rectangle((float)$input['rx'], (float)$input['ry'], (float)$input['rw'], (float)$input['rh'], 'R');
            $hit = CollisionService::pointRect($a,$b);
            break;
        case 'rect-rect':
            $a = new Rectangle((float)$input['r1x'], (float)$input['r1y'], (float)$input['r1w'], (float)$input['r1h'], 'R1');
            $b = new Rectangle((float)$input['r2x'], (float)$input['r2y'], (float)$input['r2w'], (float)$input['r2h'], 'R2');
            $hit = CollisionService::rectRect($a,$b);
            break;
        case 'circle-rect':
            $a = new Circle((float)$input['cx'], (float)$input['cy'], (float)$input['r'], 'C');
            $b = new Rectangle((float)$input['rx'], (float)$input['ry'], (float)$input['rw'], (float)$input['rh'], 'R');
            $hit = CollisionService::circleRect($a,$b);
            break;
        case 'triangle-point':
            $a = new Triangle(
                (float)$input['x1'], (float)$input['y1'],
                (float)$input['x2'], (float)$input['y2'],
                (float)$input['x3'], (float)$input['y3'],
                'T'
            );
            $b = new Point((float)$input['px'], (float)$input['py'], 'P');
            $hit = CollisionService::trianglePoint($a,$b);
            break;
        case 'triangle-rect':
            $a = new Triangle(
                (float)$input['x1'], (float)$input['y1'],
                (float)$input['x2'], (float)$input['y2'],
                (float)$input['x3'], (float)$input['y3'],
                'T'
            );
            $b = new Rectangle((float)$input['rx'], (float)$input['ry'], (float)$input['rw'], (float)$input['rh'], 'R');
            $hit = CollisionService::triangleRect($a,$b);
            break;
        case 'triangle-circle':
            $a = new Triangle(
                (float)$input['x1'], (float)$input['y1'],
                (float)$input['x2'], (float)$input['y2'],
                (float)$input['x3'], (float)$input['y3'],
                'T'
            );
            $b = new Circle((float)$input['cx'], (float)$input['cy'], (float)$input['r'], 'C');
            $hit = CollisionService::triangleCircle($a,$b);
            break;
        default:
            throw new InvalidArgumentException('Unknown case');
    }

    echo json_encode(['ok'=>true,'case'=>$case,'hit'=>$hit,'a'=>$a,'b'=>$b], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_THROW_ON_ERROR);
}
