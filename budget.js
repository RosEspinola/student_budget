
const readline = require("readline");

// PROCEDURAL: Create an interface for user input.
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

// PROCEDURAL: Function to ask for user input.
function askQuestion(message) {
    return new Promise((resolve) => {
        rl.question(message, (answer) => {
            resolve(answer);
        });
    });
}

// PROCEDURAL: Function to get a valid amount.
async function getAmount(message) {
    while (true) {
        const input = await askQuestion(message);
        const amount = Number(input);

        if (input.trim() !== "" && Number.isFinite(amount)) {
            if (amount >= 0) {
                return amount;
            } else {
                console.log("Amount cannot be negative.");
            }
        } else {
            console.log("Please enter a valid number.");
        }
    }
}

// PROCEDURAL: Function to calculate and display the report.
function calculateBudget(name, budget, expenses) {

    // PROCEDURAL: Calculate total and remaining budget.
    const total = Object.values(expenses).reduce(
        (sum, amount) => sum + amount,
        0
    );

    const remaining = budget - total;

    // PROCEDURAL: Check the spending status.
    let status;

    if (remaining < 0) {
        status = "Over Budget";
    } else if (remaining === 0) {
        status = "Budget Fully Used";
    } else {
        status = "Within Budget";
    }

    // OUTPUT: Display the budget report.
    console.log("\n========== BUDGET REPORT ==========");
    console.log("Student Name:", name);
    console.log(`Weekly Budget:     PHP ${budget.toFixed(2)}`);

    console.log("\nExpense Breakdown:");

    // PROCEDURAL: Loop through each expense.
    for (const [category, amount] of Object.entries(expenses)) {
        console.log(`${category}: PHP ${amount.toFixed(2)}`);
    }

    console.log("-----------------------------------");
    console.log(`Total Expenses:    PHP ${total.toFixed(2)}`);
    console.log(`Remaining Budget:  PHP ${remaining.toFixed(2)}`);
    console.log("Spending Status:", status);
    console.log("===================================");
}

// PROCEDURAL: Main function controls the program.
async function main() {
    console.log("STUDENT BUDGET AND EXPENSE SYSTEM");
    console.log("Node.js - Procedural Programming\n");

    // INPUT: Get the student name.
    let name = (await askQuestion("Enter student name: ")).trim();

    while (!name) {
        console.log("Student name cannot be empty.");
        name = (await askQuestion("Enter student name: ")).trim();
    }

    // INPUT: Get the weekly budget.
    const budget = await getAmount("Enter weekly budget (PHP): ");
    // INPUT: Get the four expense amounts.
    const categories = [
        "Food",
        "Transportation",
        "School Supplies",
        "Other Expenses"
    ];

    const expenses = {};

    for (const category of categories) {
        expenses[category] = await getAmount(
            `Enter ${category} expense (PHP): `
        );
    }

    // PROCEDURAL: Call the calculation function.
    calculateBudget(name, budget, expenses);

    rl.close();
}

// PROCEDURAL: Start the program.
main();
