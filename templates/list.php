<table class="default">
    <caption>
        <?= _('Alle Farben des Stud.IP Farbklimas') ?>
    </caption>
    <colgroup>
        <col>
        <col width="20%">
    </colgroup>
    <thead>
        <tr>
            <th><?= _('LESS-Index') ?></th>
            <th><?= _('Farbe') ?></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($colors as $index => $color): ?>
        <tr>
            <td><?= htmlReady($index) ?></td>
            <td>
                <div class="color-display" style="background: <?= $color ?>">
                    <?= $color ?>
                </div>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
</table>
