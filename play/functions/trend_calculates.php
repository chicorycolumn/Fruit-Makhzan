<script>

function evolve_trend_calculates_js(trends, days, overall_sales_history)
{
  trends['weather'] = weatherFromDay(days);
  trends['politics'] = politicsFromDay(days, trends['politics']);
  trends['love'] = randBetweenInclusive(1, 100);
  conf_res = conformityFromHistory(
    trends['conformity'],
    trends['conformity_history']
  );
  trends['conformity'] = conf_res['conformity'];
  trends['conformity_history'] = conf_res['conformity_history'];

  if (days % 7 == 6) {
    trends['decadence'] = decadenceFromData(overall_sales_history);
  } else {
    trends['decadence'] = trends['decadence'];
  }

  return trends;
}

function decadenceFromData(overall_sales_history)
{
  total_profit = 0;
  total_costs = 0;

  Object.keys(overall_sales_history).forEach(day => {
    total_profit += overall_sales_history[day]['profit'];
    total_costs += overall_sales_history[day]['costs'];
  })

  net = total_profit - total_costs;
  abs = Math.abs(net);
  inc = 0;

  if (0 < abs && abs < 50) {
    inc = 5;
  } else if (50 <= abs && abs < 100) {
    inc = 10;
  } else if (100 <= abs && abs < 500) {
    inc = 15;
  } else if (500 <= abs && abs < 2500) {
    inc = 20;
  } else if (2500 <= abs && abs < 10000) {
    inc = 25;
  } else if (10000 <= abs && abs < 50000) {
    inc = 30;
  } else if (50000 <= abs && abs < 500000) {
    inc = 35;
  } else if (500000 <= abs && abs < 10000000) {
    inc = 40;
  } else if (10000000 <= abs && abs < 200000000) {
    inc = 45;
  } else if (200000000 <= abs) {
    inc = 50;
  }

  if (net >= 0) {
    res = 50 + inc;
  } else {
    res = 50 - inc;
  }

  return res;
}

function randBetweenInclusive(bottom, top){
    return Math.floor(Math.random()*((top+1)-bottom))+bottom
}

function conformityFromHistory(current, hist)
{
  if (hist.slice(-1) == "s") {
    prob = randBetweenInclusive(1, 10);
    if (prob == 1) {
      return {
        "conformity": randBetweenInclusive(1, current),
        "conformity_history": hist.slice(-1) + "d",
      };
    } else if (prob == 10) {
      return {
        "conformity": randBetweenInclusive(current, 100),
        "conformity_history": hist.slice(-1) + "u",
      };
    } else {
      return {
        "conformity": current,
        "conformity_history": hist.slice(-1) + "s",
      };
    }
  } else if (hist == "uu") {
    prob = randBetweenInclusive(1, 100);
    if (prob >= 60) {
      return {
        "conformity": randBetweenInclusive(current, 100),
        "conformity_history": hist.slice(-1) + "u",
      };
    } else {
      return {
        "conformity": current,
        "conformity_history": hist.slice(-1) + "s",
      };
    }
  } else if (hist == "dd") {
    prob = randBetweenInclusive(1, 100);
    if (prob >= 60) {
      return {
        "conformity": randBetweenInclusive(1, current),
        "conformity_history": hist.slice(-1) + "d",
      };
    } else {
      return {
        "conformity": current,
        "conformity_history": hist.slice(-1) + "s",
      };
    }
  } else if (hist.slice(-1) == "u") {
    prob = randBetweenInclusive(1, 100);
    if (prob <= 5) {
      return {
        "conformity": randBetweenInclusive(1, current),
        "conformity_history": hist.slice(-1) + "d",
      };
    } else if (prob >= 65) {
      return {
        "conformity": randBetweenInclusive(current, 100),
        "conformity_history": hist.slice(-1) + "u",
      };
    } else {
      return {
        "conformity": current,
        "conformity_history": hist.slice(-1) + "s",
      };
    }
  } else if (hist.slice(-1) == "d") {
    prob = randBetweenInclusive(1, 100);
    if (prob >= 65) {
      return {
        "conformity": randBetweenInclusive(1, current),
        "conformity_history": hist.slice(-1) + "d",
      };
    } else if (prob <= 5) {
      return {
        "conformity": randBetweenInclusive(current, 100),
        "conformity_history": hist.slice(-1) + "u",
      };
    } else {
      return {
        "conformity": current,
        "conformity_history": hist.slice(-1) + "s",
      };
    }
  }
}

function weatherFromDay(days)
{
  days = days % 365;

  if (days <= 91) {
    return randBetweenInclusive(40, 100); //Spring
  } else if (91 * 1 < days && days <= 91 * 2) {
    return randBetweenInclusive(80, 100); //Summer
  } else if (91 * 2 < days && days <= 91 * 3) {
    return randBetweenInclusive(40, 80); //Autumn
  } else {
    return randBetweenInclusive(1, 40); //Winter
  }
}

function politicsFromDay(days, current)
{
  if (days % 7 == 6) {
    return randBetweenInclusive(1, 100);
  } else {
    return current;
  }
}

</script>