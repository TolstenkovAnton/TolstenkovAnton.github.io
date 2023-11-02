/*jslint browser */
function calculate_price()
{
    let product = document.getElementById("products").value;
    let n = document.getElementById("number").value;
    if (!Number.isNaN(n) && parseFloat(parseInt(n)) == parseFloat(n) && n > 0)
    {
        let cost = n * product;
        cost = cost.toFixed(2);
        document.getElementById("result").textContent = cost + " руб.";
    }
    else
    {
        document.getElementById("result").textContent = "Ошибка ввода";
    }
}