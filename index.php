<?php

const INFINITY = '∞';

function print_t($t)
{
    $text = '{';
    foreach ($t as $key => $value) {
        $text .= $value + 1;
        if ($value != end($t))
            $text .= ',';
    }
    return $text . '}';
}

function get_value($value1, $value2)
{
    if ($value1 == INFINITY || $value2 == INFINITY) {
        return INFINITY;
    } else {
        return $value1 + $value2;
    }
}

function get_closest_point($ds, $t)
{
    foreach ($t as $key => $value) {
        $closest_point = $key;
        break;
    }
    foreach ($t as $value) {
        if ($ds[$closest_point] > $ds[$value] && $ds[$value] != INFINITY) {
            $closest_point = $value;
        }
    }
    return $closest_point;
}

/*
 * Di=min{Di, Du+Ru,i}
 */
function calc_d($d, $du, $r)
{
    $temp_d = get_value($du, $r);
    if ($d == INFINITY && $temp_d == INFINITY) {
        return INFINITY;
    } elseif ($d != INFINITY && $temp_d == INFINITY) {
        return $d;
    } elseif ($d == INFINITY && $temp_d != INFINITY) {
        return $temp_d;
    } else {
        return min($d, $temp_d);
    }
}

/*
 * Calculate the shortest paths.
 */
function calc()
{
    $d[0] = $_POST['r'][0];
    $t = [];
    $html = '<div class="calculations">';
    for ($i = 1; $i < count($d[0]); $i++) {
        $t[0][$i] = $i;
    }

    for ($i = 1; $i < count($d[0]); $i++) {
        $u[] = get_closest_point($d[$i - 1], $t[$i - 1]);
        $du = $d[$i - 1][$u[$i - 1]];

        $t[$i] = $t[$i - 1];
        unset($t[$i][$u[$i - 1]]);

        if (isset($_POST['show-calculations']) && $i < count($d[0]) - 1) {
            $html .= '<hr>';

            $html .= "k = " . ($i + 1) .
                " <i>D</i><sub>u</sub>=min{<i>D</i><sub>i</sub>,i ϵ <i>T</i>}=$du, u=" . ($u[$i - 1] + 1) . ", <i>D</i><sub>u</sub>=$du, <i>T</i>=" . print_t($t[$i]) . "<br>";
        }

        foreach ($t[$i] as $value) {
            $d[$i][$value] = calc_d($d[$i - 1][$value], $du, $_POST['r'][$u[$i - 1]][$value]);

            if (isset($_POST['show-calculations'])) {
                $html .= "<i>D</i><sub>" . ($value + 1) . "</sub>=" .
                    "min{<i>D</i><sub>" . ($value + 1) . "</sub>,<i>D</i><sub>" . ($u[$i - 1] + 1) . "</sub>+<i>R</i><sub>" . ($u[$i - 1] + 1) . "," . ($value + 1) . "</sub>}=" .
                    "min{" . $d[$i - 1][$value] . "," . $du . "+" . $_POST['r'][$u[$i - 1]][$value] . "}=" .
                    "{$d[$i][$value]}<br>";
            }
        }
    }

    $html .= '<table class="table table-bordered">
    <thead>
      <tr>
        <th>k</th>';
    for ($i = 0; $i < count($_POST['r']); $i++) {
        $html .= '<th>D<sub>' . ($i + 1) . '</sub></th>';
    }
    $html .= '<th>T</th>
        </tr>
    </thead>
    <tbody>';
    for ($i = 0; $i < count($d); $i++) {
        $html .= '<tr>
        <td>' . ($i + 1) . '</td>';
        for ($j = 0; $j < count($_POST['r'][0]); $j++) {
            if ($u[$i] == $j) {
                $html .= '<td class="general-element">' . (isset($d[$i][$j]) ? $d[$i][$j] : '') . '</td>';
            } else {
                $html .= '<td>' . (isset($d[$i][$j]) ? $d[$i][$j] : '') . '</td>';
            }
        }
        $html .= '<td>' . print_t($t[$i]) . '</td>
      </tr>';
    }
    $html .= '</tbody>
        </table>';
    echo $html;
}

function load_data()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ?>
        <thead>
        <tr>
            <th></th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
        </tr>
        </thead>
        <tbody>
        <td>1</td>
        <td><input type="text" name="r[0][0]" class="form-control" value="0" readonly autocomplete="off"></td>
        <td><input type="text" name="r[0][1]" class="form-control distance" autocomplete="off"></td>
        <td><input type="text" name="r[0][2]" class="form-control distance" autocomplete="off"></td>
        </tr>
        <tr>
            <td>2</td>
            <td><input type="text" name="r[1][0]" class="form-control distance" autocomplete="off"></td>
            <td><input type="text" name="r[1][1]" class="form-control" value="0" readonly autocomplete="off"></td>
            <td><input type="text" name="r[1][2]" class="form-control distance" autocomplete="off"></td>
        </tr>
        <tr>
            <td>3</td>
            <td><input type="text" name="r[2][0]" class="form-control distance" autocomplete="off"></td>
            <td><input type="text" name="r[2][1]" class="form-control" autocomplete="off"></td>
            <td><input type="text" name="r[2][2]" class="form-control distance" value="0" readonly autocomplete="off">
            </td>
        </tr>
        </tbody><?php
    } else {
        //replace empty cells with the infinity sign
        for ($i = 0; $i < count($_POST['r']); $i++) {
            for ($j = 0; $j < count($_POST['r'][$i]); $j++) {
                if ($_POST['r'][$i][$j] == '') {
                    $_POST['r'][$i][$j] = INFINITY;
                }
            }
        }

        echo '<th></th>';
        for ($i = 0; $i < count($_POST['r']); $i++) {
            echo '<th>' . ($i + 1) . '</th>';
        }
        echo '                </tr>
                            </thead>
                            <tbody>';
        for ($i = 0; $i < count($_POST['r']); $i++) {
            echo '                <tr>' .
                '                    <td>' . ($i + 1) . '</td>';
            for ($j = 0; $j < count($_POST['r']); $j++) {
                if ($i != $j) {
                    echo '                    <td><input type="text" name="r[' . $i . '][' . $j . ']" class="form-control distance" value="' . (isset($_POST['r'][$i][$j]) ? $_POST['r'][$i][$j] : '') . '" autocomplete="off"></td>';
                } else {
                    echo '                    <td><input type="text" name="r[' . $i . '][' . $j . ']" class="form-control" value="0" readonly autocomplete="off"></td>';
                }
            }
            echo '                </tr>';
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="graph-dijkstra-figure-2.png" type="image/png">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Dijkstra</title>
</head>
<body>
<div class="content">
    <div class="col-md-10 col-md-offset-1">
        <div class="form-inline col-md-4 col-md-offset-4 header">
            <div class="form-group">
                <label for="number">Number of Nodes:</label>
                <input type="number" id="number" name="number" class="form-control" min="3"
                       value="<?php echo isset($_POST['r']) ? count($_POST['r']) : 3 ?>">
            </div>
            <button class="btn btn-primary" id="apply">Apply</button>
        </div>
    </div>
    <div class="col-md-4 col-md-offset-4">
        <form action="" method="post">
            <table class="table table-bordered" id="distance">
                <?php load_data(); ?>
            </table>
            <p class="legend">If there is no direct way between 2 nodes, leave the cell blank or Ctrl+left
                mouse click on the cell.</p>
            <div class="checkbox">
                <label><input type="checkbox"
                              name="show-calculations" <?php if (isset($_POST['show-calculations'])) echo 'checked'; ?>>Show
                    Calculations</label>
            </div>
            <button class="btn btn-primary btn-block">Solve</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            calc();
        }
        ?>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="internal.js"></script>
</body>
</html>