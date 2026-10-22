
<?php
class Budget
{
    private string $name;
    private float $budget;
    private array $expenses;

    public function __construct(
        string $name,
        float $budget,
        array $expenses
    ) {
        $this->name = $name;
        $this->budget = $budget;
        $this->expenses = $expenses;
    }

    public function getTotalExpenses(): float
    {
        return array_sum($this->expenses);
    }

    public function getRemainingBudget(): float
    {
        return $this->budget - $this->getTotalExpenses();
    }

    public function getSpendingStatus(): string
    {
        if ($this->getRemainingBudget() < 0) {
            return "Over Budget";
        } elseif ($this->getRemainingBudget() == 0) {
            return "Budget Fully Used";
        }

        return "Within Budget";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function getExpenses(): array
    {
        return $this->expenses;
    }
}

// PROCEDURAL PROGRAMMING: Process form input
$result = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $budget = $_POST["budget"] ?? "";

    $expenses = [
        "Food" => $_POST["food"] ?? "",
        "Transportation" => $_POST["transportation"] ?? "",
        "School Supplies" => $_POST["supplies"] ?? "",
        "Other Expenses" => $_POST["other"] ?? ""
    ];

    if ($name === "") {
        $error = "Please enter the student's name.";
    } elseif (
        !is_numeric($budget) ||
        !is_finite((float)$budget) ||
        $budget < 0 ||
        $budget > 5000
    ) {
        $error = "Enter a valid budget between PHP 0 and PHP 5,000.";
    } else {
        foreach ($expenses as $category => $amount) {
            if (
                !is_numeric($amount) ||
                !is_finite((float)$amount) ||
                $amount < 0
            ) {
                $error = "Enter a valid amount for " . $category . ".";
                break;
            }
        }
    }

    if ($error === "") {
        foreach ($expenses as $category => $amount) {
            $expenses[$category] = (float)$amount;
        }

        $result = new Budget(
            $name,
            (float)$budget,
            $expenses
        );
    }
}

function peso(float $amount): string
{
    return "₱" . number_format($amount, 2);
}

function old(string $field): string
{
    return htmlspecialchars(
        (string)($_POST[$field] ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Budget and Expense System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <header class="header">
        <div class="logo">SB</div>
        <p class="eyebrow">STUDENT FINANCE TRACKER</p>
        <h1>Student Budget</h1>
        <p class="subtitle">
            Plan your weekly budget and keep track of your expenses.
        </p>
    </header>

    <main>
        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">YOUR INFORMATION</p>
                    <h2>Budget Details</h2>
                </div>
                <span class="step">01</span>
            </div>

            <form method="POST" action="" id="budgetForm">

                <label for="name">Student Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?= old('name') ?>"
                    required
                >

                <label for="budget">Weekly Budget (PHP)</label>
                <input
                    type="number"
                    id="budget"
                    name="budget"
                    min="0"
                    max="5000"
                    step="0.01"
                    placeholder="e.g. 1500.00"
                    value="<?= old('budget') ?>"
                    required
                >
                <p class="field-hint">Maximum weekly budget: ₱5,000.00</p>

                <div class="section-heading expense-heading">
                    <div>
                        <p class="eyebrow">WEEKLY SPENDING</p>
                        <h2>Expense Breakdown</h2>
                    </div>
                    <span class="step">02</span>
                </div>

                <div class="expense-inputs">
                    <div class="expense-field">
                        <label for="food">Food (PHP)</label>
                        <input
                            type="number"
                            id="food"
                            name="food"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            value="<?= old('food') ?>"
                            required
                        >
                    </div>

                    <div class="expense-field">
                        <label for="transportation">Transportation (PHP)</label>
                        <input
                            type="number"
                            id="transportation"
                            name="transportation"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            value="<?= old('transportation') ?>"
                            required
                        >
                    </div>

                    <div class="expense-field">
                        <label for="supplies">School Supplies (PHP)</label>
                        <input
                            type="number"
                            id="supplies"
                            name="supplies"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            value="<?= old('supplies') ?>"
                            required
                        >
                    </div>

                    <div class="expense-field">
                        <label for="other">Other Expenses (PHP)</label>
                        <input
                            type="number"
                            id="other"
                            name="other"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            value="<?= old('other') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        Calculate Budget
                    </button>

                    <button
                        type="button"
                        class="btn-reset"
                        id="resetButton"
                    >
                        Reset Budget
                    </button>
                </div>
            </form>
        </section>

        <?php if ($error !== ""): ?>
            <div class="error-message" role="alert">
                <strong>Unable to calculate budget</strong>
                <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <?php if ($result !== null): ?>
            <?php
                $remaining = $result->getRemainingBudget();
                $total = $result->getTotalExpenses();
                $budgetAmount = $result->getBudget();

                $percentage = $budgetAmount > 0
                    ? ($total / $budgetAmount) * 100
                    : ($total > 0 ? 100 : 0);

                $barPercentage = min(100, max(0, $percentage));

                $statusClass = $remaining < 0
                    ? "status-over"
                    : ($remaining == 0 ? "status-full" : "status-within");
            ?>

            <section class="report" id="budgetReport">
                <div class="report-header">
                    <div>
                        <p class="eyebrow">YOUR SUMMARY</p>
                        <h2>Budget Report</h2>
                        <p class="report-student">
                            Prepared for
                            <strong><?= htmlspecialchars(
                                $result->getName(),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></strong>
                        </p>
                    </div>
                    <span class="status <?= $statusClass ?>">
                        <?= htmlspecialchars($result->getSpendingStatus()) ?>
                    </span>
                </div>

                <div class="summary-grid">
                    <div class="summary-card">
                        <p>Weekly Budget</p>
                        <h3><?= peso($budgetAmount) ?></h3>
                    </div>

                    <div class="summary-card">
                        <p>Total Expenses</p>
                        <h3><?= peso($total) ?></h3>
                    </div>

                    <div class="summary-card remaining-card">
                        <p>Remaining Balance</p>
                        <h3><?= peso($remaining) ?></h3>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Budget Used</span>
                        <strong><?= number_format($percentage, 1) ?>%</strong>
                    </div>
                    <div
                        class="progress-track"
                        role="progressbar"
                        aria-label="Budget used"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-valuenow="<?= $barPercentage ?>"
                    >
                        <div
                            class="progress-fill <?= $statusClass ?>"
                            style="width: <?= $barPercentage ?>%"
                        ></div>
                    </div>
                    <?php if ($percentage > 100): ?>
                        <p class="field-hint">You have exceeded your weekly budget.</p>
                    <?php endif; ?>
                </div>

                <h3 class="breakdown-title">Expense Details</h3>

                <div class="expense-list">
                    <?php foreach ($result->getExpenses() as $category => $amount): ?>
                        <div class="expense-row">
                            <span><?= htmlspecialchars($category) ?></span>
                            <strong><?= peso($amount) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="report-footer">
                    <span>Weekly spending summary</span>
                    <strong><?= peso($total) ?> spent</strong>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        Student Budget and Expense System
    </footer>

</div>

<script>
    document.getElementById("resetButton").addEventListener("click", function () {
        // Return to the clean page to clear fields, errors, and old results.
        window.location.href = window.location.pathname;
    });
</script>

</body>
</html>
