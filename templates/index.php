<table class="default" id="color-list" v-cloak>
    <caption>
        <?= _('Alle Farben des Stud.IP Farbklimas') ?>
    </caption>
    <colgroup>
        <col style="width: 20%">
        <col style="width: 20%">
        <col style="width: 20%">
        <col style="width: 20%">
        <col style="width: 20%">
    </colgroup>
    <thead>
        <tr>
            <th><?= _('CSS-Variable') ?></th>
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
            <td :title="`var(--${color.name})`">
                <input readonly type="text"
                       :value="`var(--${color.name})`"
                       @click="copy">
            </td>
            <td :title="`$${color.name}`">
                <input readonly type="text"
                       :value="`$${color.name}`"
                       @click="copy">
            </td>
            <td :title="`@${color.name}`">
                <input readonly type="text"
                       :value="`@${color.name}`"
                       @click="copy">
            </td>
            <td v-if="searchColor">
                {{ color.distance(searchColor).toFixed(2) }}
            </td>
            <td v-else></td>
            <td>
                <div class="color-display" :style="`background: var(--${color.name})`">
                    {{ color.hex }}
                </div>
            </td>
        </tr>
    </tbody>
</table>
