<script>

function calculateSales() {
  let incipient_sales = {};

  let example_name = $("table#inventory tbody tr").find(".nameData").text()

  incipient_sales[example_name] = { "sales_quantity": 0, "profit": 0 };

  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    let name = row.find(".nameData").text();
    let quantity = digitGrouping(row.find(".quantityData").text(), true);
    let selling_price = digitGrouping(row.find(".sellingPriceData").text(), true);

    if (row.hasClass("hidden") || !selling_price) {
      return;
    }

    let max_prices = parseIntObjectValues(
      JSON.parse(row.find(".maxPricesData").text())
    );
    let popularity_factors = JSON.parse(row.find(".popFactorsData").text());
    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates,
      name
    );

    let price_disparity =
      ((max_buying_price - selling_price) / max_buying_price) * 100;

    let sales_percentage = (popularity + price_disparity * 4) / 5 / 100;

    let sales_quantity = Math.ceil(sales_percentage * quantity);

    let copy_of_sales_quantity_before_plusminus = sales_quantity

    let plusOrMinusFive = (Math.round(Math.random() * 10) - 5)*4;

    sales_quantity += Math.round((plusOrMinusFive / 100) * sales_quantity);

    if (sales_quantity < 0) {
      sales_quantity = 0;
    } else if (sales_quantity > quantity) {
      sales_quantity = quantity;
    }

    sales_quantity = Math.round(sales_quantity);
    let profit = Math.round(sales_quantity * selling_price);
    profit = Math.round(profit);
    incipient_sales[name] = { sales_quantity, profit };
  });

  // console.log(incipient_sales)
  return incipient_sales;
}

function getSalesSubstrates(
  popularity_factors,
  max_prices,
  trend_calculates,
  name
) {
  let factor1 = getPopularityFactor(popularity_factors, 0, trend_calculates);
  let factor2 = getPopularityFactor(popularity_factors, 1, trend_calculates);
  let popularity = Math.ceil((factor1 * 3 + factor2) / 4);

  let range = max_prices["High"] - max_prices["Low"];
  let fraction_of_price_range = Math.round(
    (Math.floor((popularity - 1) / 20) / 4) * range
  );
  let max_buying_price = Math.round(
    max_prices["Low"] + fraction_of_price_range
  );
  let restock_price = Math.ceil(0.8 * max_buying_price);

  return { popularity, max_buying_price, restock_price };
}

function updateSalesSubstratesInDisplayedTable() {
  $("table#inventory tbody tr").each(function () {
    let row = $(this);
    let name = row.find(".nameData").text();

    if (row.hasClass("hidden")) {
      return;
    }

    let max_prices = parseIntObjectValues(
      JSON.parse(row.find(".maxPricesData").text())
    );
    let popularity_factors = JSON.parse(row.find(".popFactorsData").text());

    let { popularity, max_buying_price, restock_price } = getSalesSubstrates(
      popularity_factors,
      max_prices,
      trend_calculates,
      name
    );

    row.find(".devdata1").text("P" + popularity + "  M" + max_buying_price);

    row
      .find(".popularityCircleText")
      .text(getPopularityColor(popularity).text);

    row
      .find(".popularityCircleSpan")
      .css({ "background-color": getPopularityColor(popularity).color });

    row
    .find(".popularityCircleTooltip")
    .text(name + " has " + getPopularityColor(popularity).descrip.slice(0, -1) + " popularity" + getPopularityColor(popularity).descrip.slice(-1));

    function getPopularityColor(pop) {
      if (pop < 20) {
        return { text: "⇊", color: "red", descrip: "very low." };
      } else if (pop < 40) {
        return { text: "↓", color: "orange", descrip: "low." };
      } else if (pop < 60) {
        return { text: "·", color: "yellow", descrip: "medium." };
      } else if (pop < 80) {
        return { text: "↑", color: "greenyellow", descrip: "high." };
      } else if (pop >= 80) {
        return { text: "⇈", color: "cyan", descrip: "very high." };
      }
    }

    row.find(".restockPriceData").text(digitGrouping(restock_price));
  });
}

</script>