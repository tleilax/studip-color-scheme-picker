<table class="default" id="color-list" v-cloak>
    <caption>
        <?= _('Alle Farben des Stud.IP Farbklimas') ?>
    </caption>
    <colgroup>
        <col width="25%">
        <col width="25%">
        <col width="25%">
        <col width="25%">
    </colgroup>
    <thead>
        <tr>
            <th><?= _('SCSS-Index') ?></th>
            <th><?= _('LESS-Index') ?></th>
            <th v-if="searchColor">
                <abbr title="<?= _('Kleiner ist besser') ?>">
                    <?= _('Übereinstimmung') ?>
                </abbr>
            </th>
            <th v-else></th>
            <th><?= _('Farbe') ?></th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="color in sortedColors" :key="color.name">
            <td>
                <input readonly type="text"
                       :value="`$${color.name}`"
                       @click="copy">
            </td>
            <td>
                <input readonly type="text"
                       :value="`@${color.name}`"
                       @click="copy">
            </td>
            <td v-if="color.distance">
                {{ color.distance }}
            </td>
            <td v-else></td>
            <td>
                <div class="color-display" :style="`background: var(--${color.name})`">
                    {{ color.color }}
                </div>
            </td>
        </tr>
    </tbody>
</table>
