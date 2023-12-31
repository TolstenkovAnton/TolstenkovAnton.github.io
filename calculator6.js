/*jslint browser */
let checked_type = -1;
function display_on(elem_id)
{
    console.log(elem_id + " display_on");
    document.getElementById(elem_id).style.display = "flex";
}

function display_off(elem_id)
{
    console.log(elem_id + " display_off");
    document.getElementById(elem_id).style.display = "none";
}

function backpack_extend()
{
    display_on("backpack_add");
    display_off("pen_add");
    checked_type = 0;
}

function pen_extend()
{
    display_on("pen_add");
    display_off("backpack_add");
    checked_type = 1;
}

function pencilbox_extend()
{
    display_off("pen_add");
    display_off("backpack_add");
    checked_type = 2;
}

function calculate_price_items()
{
    let n = document.getElementById("number_items").value;
    if (!Number.isNaN(n) && parseFloat(parseInt(n)) == parseFloat(n) && n >= 1)
    {
        console.log(checked_type);
        switch (checked_type)
        {
            case 0:
            {
                console.log("case0");
                let cost = 3990 * n;
                let checkboxes = document.querySelectorAll(".checkbox_backpack input[type='checkbox']");
                console.log(checkboxes);
                for (let i = 0; i < checkboxes.length; i++) 
                {
                    if (checkboxes[i].checked) 
                    {
                        cost += parseInt(checkboxes[i].value);
                    }
                }
                cost = cost.toFixed(2);
                document.getElementById("result").textContent = cost + " руб.";
                break;
            }
            case 1:
            {
                console.log("case1");
                let cost = document.getElementById("colors").value * n;
                cost = cost.toFixed(2);
                document.getElementById("result").textContent = cost + " руб.";
                break;
            }
            case 2:
            {
                console.log("case2");
                let cost = 400 * n;
                cost = cost.toFixed(2);
                document.getElementById("result").textContent = cost + " руб.";
                break;
            }
        }
    }
    else
    {
        document.getElementById("result").textContent = "Ошибка ввода";
    }
}