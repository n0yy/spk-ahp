<?php

function normalizeMatrix($matrix) {
    $normalized = [];
    $colSums = array_fill_keys(array_keys($matrix), 0);
    foreach ($matrix as $rowKey => $row) {
        foreach ($row as $colKey => $value) {
            $colSums[$colKey] += $value;
        }
    }

    foreach ($matrix as $rowKey => $row) {
        foreach ($row as $colKey => $value) {
            $normalized[$rowKey][$colKey] = $value / $colSums[$colKey];
        }
    }
    return $normalized;
}

function calculateWeights($normalizedMatrix) {
    $weights = [];
    foreach ($normalizedMatrix as $rowKey => $row) {
        $weights[$rowKey] = array_sum($row) / count($row);
    }
    return $weights;
}

function calculateConsistency($matrix, $weights) {
    $lambdaMax = 0;
    $n = count($matrix);
    $colSums = array_fill_keys(array_keys($matrix), 0);
    foreach ($matrix as $rowKey => $row) {
        foreach ($row as $colKey => $value) {
            $colSums[$colKey] += $value;
        }
    }

    foreach ($weights as $key => $weight) {
        $lambdaMax += $colSums[$key] * $weight;
    }

    $ci = ($lambdaMax - $n) / ($n - 1);
    $ri = [1 => 0, 2 => 0, 3 => 0.58, 4 => 0.90, 5 => 1.12, 6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49];
    $cr = ($n > 2) ? $ci / $ri[$n] : 0;

    return ['lambda_max' => $lambdaMax, 'ci' => $ci, 'cr' => $cr];
}

?>