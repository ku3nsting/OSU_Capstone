<?php
require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/datasets.php';

$mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

if ($mysqli->connect_error) {
	die('Connection failed: ' . $mysqli->connect_error);
}

// retrieve the award types
$awardTypesStmt = $mysqli->prepare("SELECT * FROM Awards");
if(!$awardTypesStmt->execute()) {
    error_log('Failed to get award types');
    exit();
}
$awardTypes = $awardTypesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach($awardTypes as $awardType) {
    $awardDateIterator = $awardDateIteratorLimit = new DateTime();
    switch ($awardType['AwardType']) {
        case 'employee-of-the-month':
            $awardDateIteratorLimit = (new DateTime())->sub(new DateInterval('P1M'));
            $awardDateIterator = new DateTime('2015-01-01');
            $interval = new DateInterval('P1M');
            $awardDateFormat = 'Y-m-t';
            break;
        case 'employee-of-the-week':
        case 'peer':
        default:
            $awardDateIteratorLimit = new DateTime();
            $awardDateIterator = new DateTime('2015-01-09');
            $interval = new DateInterval('P1W');
            $awardDateFormat = 'Y-m-d';
            break;
    }

    while ($awardDateIterator < $awardDateIteratorLimit) {
        $awardDateStr = $awardDateIterator->format($awardDateFormat);
        $query = "
            SELECT *
            FROM Employees
            JOIN UserType ON Employees.ID = UserType.EmployeeID
            WHERE UserType.Type = 'user'
            AND hireDate < ?
        ";
        $employeeStmt = $mysqli->prepare($query);
        $employeeStmt->bind_param('s', $awardDateStr);
        if (!$employeeStmt->execute()) {
            error_log('Failed to get employees');
            exit;
        }

        $employeesResult = $employeeStmt->get_result();
        if ($employeesResult->num_rows >= 2) {
            $employees = $employeesResult->fetch_all(MYSQLI_ASSOC);

            $randAwardeeIndex = rand(0, ($employeesResult->num_rows - 1));
            $randGiverIndex = $randAwardeeIndex;
            while ($randAwardeeIndex === $randGiverIndex) {
                $randGiverIndex = rand(0, ($employeesResult->num_rows - 1));
            }

            $awardee = $employees[$randAwardeeIndex];
            $giver = $employees[$randGiverIndex];

            $insertAwardStmt = $mysqli->prepare(
                "INSERT INTO Awards_Given (AwardID, EmployeeID, AwardDate, AwardedByID) VALUES (?, ?, ?, ?)"
            );
            $awardTypeId = $awardType['ID'];
            $employeeId = $awardee['EmployeeID'];
            $adminUserId = $giver['EmployeeID'];
            $insertAwardStmt->bind_param(
                'iisi',
                $awardTypeId,
                $employeeId,
                $awardDateStr,
                $adminUserId
            );

            if (!$insertAwardStmt->execute()) {
                error_log($insertAwardStmt->error);
            }
            $insertAwardStmt->close();
        }

        $employeeStmt->close();
        $awardDateIterator = $awardDateIterator->add($interval);
    }
}

echo "done\n";
