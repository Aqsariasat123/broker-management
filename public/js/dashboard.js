// Income vs Expense Pie Chart
const incomeExpenseCtx = document.getElementById('incomeExpenseChart');
if (incomeExpenseCtx) {
  window.incomeExpenseChart = new Chart(incomeExpenseCtx, {
    type: 'pie',
    data: {
      labels: ['Income', 'Expense'],
      datasets: [{
        data: [incomeExpenseTotalIncome, incomeExpenseTotalExpense],
        backgroundColor: ['#6c757d', '#dc3545'],
        borderWidth: 2,
        borderColor: '#2d2d2d'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: {
          top: 5,
          bottom: 5,
          left: 5,
          right: 5
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.label + ': ' + number_format(context.parsed, 2);
            }
          }
        }
      }
    }
  });
}

// Income Chart
const incomeCtx = document.getElementById('incomeChart');
if (incomeCtx) {
  // Normalize data to 0-10 scale for Y-axis
  const incomeValues = incomeMonthlyData.map(d => d.income);
  const maxIncome = Math.max(...incomeValues, 1);
  const normalizedIncome = incomeValues.map(val => (val / maxIncome) * 10);
  
  window.incomeChart = new Chart(incomeCtx, {
    type: 'bar',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 
               'July', 'August', 'September', 'October', 'November', 'December'],
      datasets: [{
        label: 'Income',
        data: normalizedIncome,
        backgroundColor: '#17a2b8'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 10,
          ticks: {
            stepSize: 1
          }
        },
        x: {
          ticks: {
            font: {
              size: 9
            },
            maxRotation: 45,
            minRotation: 45
          },
          grid: {
            display: false
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const index = context.dataIndex;
              return 'Income: ' + number_format(incomeValues[index], 2);
            }
          }
        }
      }
    }
  });

  // Add month stats - format: percentage, value, and "Sells" label in separate rows
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
  const incomeStatsHtml = incomeMonthlyData.map((d, idx) => 
    `<div class="month-stat-item"><div>${d.income_percent}%</div><div>${d.sells}</div><div>Sells</div></div>`
  ).join('');
  document.getElementById('incomeStats').innerHTML = incomeStatsHtml;
}

// Expense Chart
const expenseCtx = document.getElementById('expenseChart');
if (expenseCtx) {
  // Normalize data to 0-10 scale for Y-axis
  const expenseValues = expenseMonthlyData.map(d => d.expense);
  const maxExpense = Math.max(...expenseValues, 1);
  const normalizedExpense = expenseValues.map(val => (val / maxExpense) * 10);
  
  window.expenseChart = new Chart(expenseCtx, {
    type: 'bar',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 
               'July', 'August', 'September', 'October', 'November', 'December'],
      datasets: [{
        label: 'Expenses',
        data: normalizedExpense,
        backgroundColor: '#17a2b8'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          max: 10,
          ticks: {
            stepSize: 1
          }
        },
        x: {
          ticks: {
            font: {
              size: 9
            },
            maxRotation: 45,
            minRotation: 45
          },
          grid: {
            display: false
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const index = context.dataIndex;
              return 'Expense: ' + number_format(expenseValues[index], 2);
            }
          }
        }
      }
    }
  });

  // Add month stats - format: percentage, value, and "Sells" label in separate rows
  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
  const expenseStatsHtml = expenseMonthlyData.map((d, idx) => 
    `<div class="month-stat-item"><div>${d.expense_percent}%</div><div>${d.sells}</div><div>Sells</div></div>`
  ).join('');
  document.getElementById('expenseStats').innerHTML = expenseStatsHtml;
}

// Helper function for number formatting
function number_format(num, decimals) {
  return parseFloat(num).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function updateYear(year) {
  const url = new URL(window.location.href);
  url.searchParams.set('year', year);
  window.location.href = url.toString();
}

function updateChartYear(chartType, year) {
  // Make AJAX request to update only the specific chart
  fetch(`${dashboardRoute}?${chartType}Year=${year}`, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (chartType === 'incomeExpense') {
      updateIncomeExpenseChart(data);
    } else if (chartType === 'income') {
      updateIncomeChart(data);
    } else if (chartType === 'expense') {
      updateExpenseChart(data);
    }
  })
  .catch(error => {
    console.error('Error updating chart:', error);
    // Fallback to page reload
    const url = new URL(window.location.href);
    url.searchParams.set(`${chartType}Year`, year);
    window.location.href = url.toString();
  });
}

function updateIncomeExpenseChart(data) {
  // Update date range inputs
  const dateRangeEl = document.getElementById('incomeExpenseDateRange');
  if (dateRangeEl) {
    dateRangeEl.querySelector('.date-range-item:first-child input.date-input').value = data.yearStart;
    dateRangeEl.querySelector('.date-range-item:first-child input.amount-input').value = parseFloat(data.totalIncome).toFixed(2);
    dateRangeEl.querySelector('.date-range-item:last-child input.date-input').value = data.yearEnd;
    dateRangeEl.querySelector('.date-range-item:last-child input.amount-input').value = parseFloat(data.totalExpense).toFixed(2);
  }
  
  // Update chart
  if (window.incomeExpenseChart) {
    window.incomeExpenseChart.data.datasets[0].data = [data.totalIncome, data.totalExpense];
    window.incomeExpenseChart.update();
  }
}

function updateIncomeChart(data) {
  // Update chart and stats
  const incomeValues = data.monthlyData.map(d => d.income);
  const maxIncome = Math.max(...incomeValues, 1);
  const normalizedIncome = incomeValues.map(val => (val / maxIncome) * 10);
  
  if (window.incomeChart) {
    window.incomeChart.data.datasets[0].data = normalizedIncome;
    window.incomeChart.update();
  }
  
  // Update month stats
  const incomeStatsHtml = data.monthlyData.map((d, idx) => 
    `<div class="month-stat-item"><div>${d.income_percent}%</div><div>${d.sells}</div><div>Sells</div></div>`
  ).join('');
  document.getElementById('incomeStats').innerHTML = incomeStatsHtml;
}

function updateExpenseChart(data) {
  // Update chart and stats
  const expenseValues = data.monthlyData.map(d => d.expense);
  const maxExpense = Math.max(...expenseValues, 1);
  const normalizedExpense = expenseValues.map(val => (val / maxExpense) * 10);
  
  if (window.expenseChart) {
    window.expenseChart.data.datasets[0].data = normalizedExpense;
    window.expenseChart.update();
  }
  
  // Update month stats
  const expenseStatsHtml = data.monthlyData.map((d, idx) => 
    `<div class="month-stat-item"><div>${d.expense_percent}%</div><div>${d.sells}</div><div>Sells</div></div>`
  ).join('');
  document.getElementById('expenseStats').innerHTML = expenseStatsHtml;
}

function exportDashboard() {
  const dateRange = document.querySelector('select[name="date_range"]').value;
  window.location.href = `${dashboardExportRoute}?date_range=${dateRange}`;
}