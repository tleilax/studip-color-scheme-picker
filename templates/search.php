<form action="<?= $action_url ?>" method="get">
    <fieldset>
        <legend><?= _('Ähnliche Farbe suchen') ?></legend>

        <section>
            <label for="color"><?= _('Farbwert') ?></label>
            <input type="text" name="color" maxlength="7" placeholder="#rrggbb"
                   autofocus pattern="#?[a-fA-F0-9]{3}([a-fA-F0-9]{3})?"
                   value="<?= @$color ?>">
        </section>
    </fieldset>

    <footer>
        <?= Studip\Button::create(_('Suchen')) ?>
    </footer>
</form>

<? if (isset($distances) && isset($color)): ?>
<table class="default">
    <caption>
        <?= sprintf(_('Ähnliche gefundene Farben zu %s'), $color) ?>
    </caption>
    <colgroup>
        <col width="20px">
        <col>
        <col width="100px">
        <col width="20%">
        <col width="20%">
    </colgroup>
    <thead>
        <tr>
            <th>#</th>
            <th><?= _('LESS-Index') ?></th>
            <th><?= _('Distanz') ?></th>
            <th><?= _('Farbe') ?></th>
            <th><?= _('Original') ?></th>
        </tr>
    </thead>
    <tbody>
    <? $i = 1; foreach ($distances as $index => $distance): ?>
        <tr>
            <td><?= $i ?></td>
            <td onclick="this.select();"><?= htmlReady($index) ?></td>
            <td><?= round($distance, 2) ?></td>
            <td>
                <div class="color-display" style="background: <?= $colors[$index] ?>">
                    <?= $colors[$index] ?>
                </div>
            </td>
            <td>
                <div class="color-display" style="background: <?= $color ?>">
                    <?= $color ?>
                </div>
            </td>
        </tr>
    <? $i += 1; endforeach; ?>
    </tbody>
</table>
<? endif; ?>
