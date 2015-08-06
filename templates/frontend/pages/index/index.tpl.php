<?php

$x = 55;
$y = 0;
$coef = .6;

?>
<style>
    #trapezoid-left {
        border-bottom: <?= floor($coef * 130) ?>px solid #414042;
        border-left: <?= floor($coef * 75) ?>px solid transparent;
        border-right: <?= floor($coef * 75) ?>px solid transparent;
        height: 0; width: <?= floor($coef * 450) ?>px;
        margin:0 auto;
        position:relative;
        left:<?= $x + floor($coef * -270) ?>px;
        top:<?= $y + floor($coef * 97) ?>px;
        transform: rotate(120deg);
    }
    #trapezoid-right {
        border-bottom: <?= floor($coef * 130) ?>px solid #1c75bc;
        border-left: <?= floor($coef * 75) ?>px solid transparent;
        border-right: <?= floor($coef * 75) ?>px solid transparent;
        height: 0; width: <?= floor($coef * 450) ?>px;
        margin:0 auto;
        position:relative;
        left:<?= $x + floor($coef * 7) ?>px;
        top:<?= $y + floor($coef * -185) ?>px;
        transform: rotate(240deg);
    }
    #trapezoid-bottom {
        border-bottom: <?= floor($coef * 130) ?>px solid #414042;
        border-left: <?= floor($coef * 75) ?>px solid transparent;
        border-right: <?= floor($coef * 75) ?>px solid transparent;
        height: 0; width: <?= floor($coef * 450) ?>px;
        margin:0 auto;
        position:relative;
        left:<?= $x ?>px;
        top:<?= $y ?>px;
    }
</style>
<div class="outer" style="display:table;position:absolute;height:100%;width:100%;" />
<div style="text-align:center;width:100%;display:table-cell;vertical-align: middle;">
    <div style="margin-bottom:30px">
        <div id="trapezoid-left"></div>
        <div id="trapezoid-right"></div>
        <div id="trapezoid-bottom"></div>
    </div>
    <font style="color:#1c75bc">
        <font style="font-size:100px">Asymptix PHP Framework</font><br />
        <font style="font-size:50px">Let's start! Just do it!</font>
    </font>
</div>
<div class="inner" style="margin-left:auto;margin-right:auto;" />