<?php
class Budget
{
    // Encapsulation: private properties
    private string $name;
    private float $budget;
    private array $expenses;

    // Constructor: receives the student's information
    public function __construct(
        string $name,
        float $budget,
        array $expenses
    ) {
        $this->name = $name;
        $this->budget = $budget;
        $this->expenses = $expenses;
    }

    // Calculate total expenses
    public function getTotalExpenses(): float
    {
        return array_sum($this->expenses);
    }

    // Calculate remaining budget
    public function getRemainingBudget(): float
    {
        return $this->budget - $this->getTotalExpenses();
    }

    // Check spending status
    public function getSpendingStatus(): string
    {
        if ($this->getRemainingBudget() < 0) {
            return "Over Budget";
        } elseif ($this->getRemainingBudget() == 0) {
            return "Budget Fully Used";
        } else {
            return "Within Budget";
        }
    }

    // Get student name
    public function getName(): string
    {
        return $this->name;
    }

    // Get weekly budget
    public function getBudget(): float
    {
        return $this->budget;
    }

    // Get all expenses
    public function getExpenses(): array
    {
        return $this->expenses;
    }
}


// ==================================================
// SECTION 2: PROCEDURAL PROGRAMMING
// Find this section to change input validation.
// ==================================================

$result = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get student name and budget from the form
    $name = trim($_POST["name"] ?? "");
    $budget = $_POST["budget"] ?? "";

    // Get expense amounts from the form
    $expenses = [
        "Food" => $_POST["food"] ?? "",
        "Transportation" => $_POST["transportation"] ?? "",
        "School Supplies" => $_POST["supplies"] ?? "",
        "Other Expenses" => $_POST["other"] ?? ""
    ];


    // ----------------------------------------------
    // SECTION 3: VALIDATE STUDENT NAME
    // Change this section if name validation is needed.
    // ----------------------------------------------

    if ($name === "") {
        $error = "Please enter the student's name.";
    }


    // ----------------------------------------------
    // SECTION 4: VALIDATE WEEKLY BUDGET
    // BUDGET LIMIT: Find and change this section.
    // ----------------------------------------------

    elseif (!is_numeric($budget) || $budget < 0) {
        $error = "Please enter a valid budget.";
    }

    // >>> MAXIMUM BUDGET LIMIT: CHANGE OR REMOVE HERE <<<
    elseif ($budget > 5000) {
        $error = "Budget cannot exceed PHP 5000.";
    }
    // To remove the maximum limit, delete the
    // elseif block above. Keep the validation before it.


    // ----------------------------------------------
    // SECTION 5: VALIDATE EXPENSES
    // Expenses must be numbers and cannot be negative.
    // ----------------------------------------------

    else {
        foreach ($expenses as $category => $amount) {
            if (!is_numeric($amount) || $amount < 0) {
                $error = "Please enter a valid amount for "
                       . $category . ".";
                break;
            }
        }
    }


    // ----------------------------------------------
    // SECTION 6: CREATE THE BUDGET OBJECT
    // Runs only when all inputs are valid.
    // ----------------------------------------------

    if ($error === "") {
        foreach ($expenses as $category => $amount) {
            $expenses[$category] = (float) $amount;
        }

        $result = new Budget(
            $name,
            (float) $budget,
            $expenses
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Budget and Expense System</title>

    <!-- SECTION 7: EXTERNAL CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <!-- SECTION 8: PAGE TITLE -->
    <header class="header">
        <h1>Student Budget and Expense System</h1>
        <p>Manage your weekly budget and expenses.</p>
    </header>


    <!-- ==========================================
         SECTION 9: GUI INPUT FORM
         Find this section to change input fields.
         ========================================== -->

    <section class="card">
        <h2>Enter Budget Information</h2>

        <form method="POST" action="">

            <!-- Student Name -->
            <label for="name">Student Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                required
            >

            <!-- >>> HTML BUDGET LIMIT: CHANGE OR REMOVE HERE <<< -->
            <label for="budget">Weekly Budget (PHP)</label>
            <input
                type="number"
                id="budget"
                name="budget"
                min="0"
                max="5000"
                step="0.01"
                placeholder="Enter your weekly budget"
                value="<?= htmlspecialchars($_POST['budget'] ?? '') ?>"
                required
            >
            <!-- To remove the HTML limit, delete max="5000".
                 Also remove the PHP maximum limit in Section 4. -->


            <!-- SECTION 10: EXPENSE INPUTS -->

            <h3>Expenses</h3>

            <label for="food">Food (PHP)</label>
            <input
                type="number"
                id="food"
                name="food"
                min="0"
                step="0.01"
                placeholder="Enter food expenses"
                value="<?= htmlspecialchars($_POST['food'] ?? '') ?>"
                required
            >

            <label for="transportation">Transportation (PHP)</label>
            <input
                type="number"
                id="transportation"
                name="transportation"
                min="0"
                step="0.01"
                placeholder="Enter transportation expenses"
                value="<?= htmlspecialchars($_POST['transportation'] ?? '') ?>"
                required
            >

            <label for="supplies">School Supplies (PHP)</label>
            <input
                type="number"
                id="supplies"
                name="supplies"
                min="0"
                step="0.01"
                placeholder="Enter school supplies expenses"
                value="<?= htmlspecialchars($_POST['supplies'] ?? '') ?>"
                required
            >

            <label for="other">Other Expenses (PHP)</label>
            <input
                type="number"
                id="other"
                name="other"
                min="0"
                step="0.01"
                placeholder="Enter other expenses"
                value="<?= htmlspecialchars($_POST['other'] ?? '') ?>"
                required
            >


            <!-- SECTION 11: FORM BUTTONS -->

            <div class="button-group">
                <button type="submit">Calculate Budget</button>
                <button type="reset">Reset Budget</button>
            </div>

        </form>
    </section>


    <!-- ==========================================
         SECTION 12: ERROR MESSAGE
         Displays invalid input messages.
         ========================================== -->

    <?php if ($error !== ""): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>


    <!-- ==========================================
         SECTION 13: OUTPUT / BUDGET REPORT
         Find this section to change displayed results.
         ========================================== -->

    <?php if ($result !== null): ?>

        <section class="card result-card">

            <h2>Budget Report</h2>

            <p>
                <strong>Student Name:</strong>
                <?= htmlspecialchars($result->getName()) ?>
            </p>

            <p>
                <strong>Weekly Budget:</strong>
                PHP <?= number_format($result->getBudget(), 2) ?>
            </p>

            <h3>Expense Breakdown</h3>

            <div class="expense-list">
                <?php foreach ($result->getExpenses() as $category => $amount): ?>
                    <p>
                        <span><?= htmlspecialchars($category) ?></span>
                        <strong>PHP <?= number_format($amount, 2) ?></strong>
                    </p>
                <?php endforeach; ?>
            </div>

            <hr>

            <p>
                <strong>Total Expenses:</strong>
                PHP <?= number_format($result->getTotalExpenses(), 2) ?>
            </p>

            <p>
                <strong>Remaining Budget:</strong>
                PHP <?= number_format($result->getRemainingBudget(), 2) ?>
            </p>

            <p>
                <strong>Spending Status:</strong>
                <?= htmlspecialchars($result->getSpendingStatus()) ?>
            </p>

        </section>

    <?php endif; ?>

</div>

</body>
</html>