(function () {
    /**
     * Convert hex color to rgb (with optional monochrome value)
     * @param  String  hex         Hex color
     * @param  Boolean includeMono Include monochrome value
     * @return Object with r, g, b and monochrome value
     */
    function hexToRgb (hex, includeMono = true) {
        if (hex[0] === '#') {
            hex = hex.slice(1);
        }
        const rgb = {
            r: parseInt(hex.slice(0, 2), 16),
            g: parseInt(hex.slice(2, 4), 16),
            b: parseInt(hex.slice(4, 6), 16),
        }
        if (includeMono) {
            rgb.mono = 0.2125 * rgb.r + 0.7154 * rgb.g + 0.0721 * rgb.b;
        }
        return rgb;
    }

    /**
     * Calculates the distance between to colors
     * @param  Object color0 RGB color
     * @param  Object color1 RGB color
     * @return Number
     */
    function colorDistance (color0, color1) {
        const distance = {
            r: Math.abs(color0.r - color1.r),
            g: Math.abs(color0.g - color1.g),
            b: Math.abs(color0.b - color1.b),
            mono: Math.abs(color0.mono - color1.mono),
        };

        return Math.sqrt(
            Math.pow(distance.r, 2) +
            Math.pow(distance.g, 2) +
            Math.pow(distance.b, 2) +
            Math.pow(distance.mono, 2)
        );
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Get all css color variables
        // (see https://stackoverflow.com/a/54851636/982902)
        const colors = Array.from(document.styleSheets).filter(sheet => {
            return sheet.href === null || sheet.href.startsWith(window.location.origin);
        }).reduce((allVars, sheet) => {
            const style = getComputedStyle(document.body);
            return allVars.concat(
                ...Array.from(sheet.cssRules).reduce((vars, rule) => {
                    if (rule.selectorText !== ':root') {
                        return vars;
                    }
                    return vars.concat(...Array.from(rule.style).filter(name => {
                        return name.startsWith('--');
                    }).reduce((defs, name) => {
                        const color = style.getPropertyValue(name);
                        if (color.match(/^#[0-9a-f]{6}$/)) {
                            defs.push({
                                name: name.slice(2),
                                color: color,
                                rgb: hexToRgb(color),
                            });
                        }
                        return defs;
                    }, []));
                }, [])
            );
        }, []);

        // Create vue app
        const app = new Vue({
            el: document.getElementById('color-list'),
            data: {
                colors: colors,
                searchColor: false
            },
            methods: {
                copy (event) {
                    const dummy = document.createElement('textarea');
                    dummy.value = event.target.value;
                    document.body.appendChild(dummy);
                    dummy.select();
                    if (document.execCommand('copy')) {
                        event.target.classList.add('copied-value');
                        event.target.addEventListener('animationend', () => {
                            event.target.classList.remove('copied-value');
                        });
                    }
                    document.body.removeChild(dummy);
                }
            },
            computed: {
                sortedColors () {
                    if (!this.searchColor) {
                        return this.colors;
                    }
                    const rgb = hexToRgb(this.searchColor);
                    const distances = this.colors.map((color, index) => {
                        return {
                            distance: colorDistance(color.rgb, rgb),
                            index: index
                        };
                    });
                    distances.sort((a, b) => {
                        return a.distance - b.distance;
                    });

                    return distances.reduce((colors, item) => {
                        const color = this.colors[item.index];
                        color.distance = parseFloat(item.distance).toFixed(2);
                        colors.push(color);
                        return colors;
                    }, []);
                }
            }
        });

        // Attach search input from sidebar
        const search = document.querySelector('.sidebar input[name="search-color"]');
        search.addEventListener('keyup', event => {
            if (search.checkValidity()) {
                const color = search.value.toLowerCase();
                if (color.length > 0 && color[0] === '#') {
                    color = color.slice(1);
                }
                app.searchColor = color;
            } else {
                app.searchColor = false;
            }
        });
        search.closest('form').addEventListener(
            'submit',
            event => event.preventDefault()
        );
    });
}());
