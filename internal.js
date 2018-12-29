$('.distance').on('mousedown', function (e) {
    if (e.button == 0 && e.ctrlKey) {
        $(this).val('∞');
    }
});

$("#apply").on("click", function () {
    var distance_html = '<thead>\n' +
        '                <tr>\n' +
        '                    <th></th>\n';
    for (i = 0; i < $("#number").val(); i++) {
        distance_html += '<th>' + (i + 1) + '</th>';
    }
    distance_html += '                </tr>\n' +
        '                </thead>\n' +
        '                <tbody>\n';
    for (i = 0; i < $("#number").val(); i++) {
        distance_html += '                <tr>\n' +
            '                    <td>' + (i + 1) + '</td>\n';
        for (j = 0; j < $("#number").val(); j++) {
            if (i != j) {
                distance_html += '                    <td><input type="text" name="r[' + i + '][' + j + ']" class="form-control distance" autocomplete="off"></td>\n';
            } else {
                distance_html += '                    <td><input type="text" name="r[' + i + '][' + j + ']" readonly class="form-control" value="0" autocomplete="off"></td>\n';
            }
        }
        distance_html += '                </tr>\n';
    }
    distance_html += '                </tbody>';
    $("#distance").html(distance_html);
    $('.distance').on('mousedown', function (e) {
        if (e.button == 0 && e.ctrlKey) {
            $(this).val('∞');
        }
    });

})