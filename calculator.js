function calculate_price()
{
    console.log("Я запустился");
    let product = document.getElementById("products").value;
    let n = document.getElementById("number").value;
    if (!isNaN(n) && parseFloat(parseInt(n)) == parseFloat(n) && n > 0)
    {
        let cost = n * product;
        cost = cost.toFixed(2);
        document.getElementById("result").textContent = cost + " руб.";
    }
    else
    {
        document.getElementById("result").textContent = "Неправильный формат ввода кол-ва товара";
    }
}