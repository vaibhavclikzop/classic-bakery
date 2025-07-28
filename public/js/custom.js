


function formatQtyPrice(v) {
  v = parseFloat(v);
 
    if (v % 1 === 0 || Number(v.toFixed(2)) === v) {
        return v.toFixed(2);
    }

    return v.toString();

}