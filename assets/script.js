(function () {
    class Color {
        static fromHexValue(hex, name) {
            if (hex[0] === '#') {
                hex = hex.slice(1);
            }

            const rgb = {
                r: parseInt(hex.slice(0, 2), 16),
                g: parseInt(hex.slice(2, 4), 16),
                b: parseInt(hex.slice(4, 6), 16),
            }

            return new Color(rgb.r, rgb.g, rgb.b, name);
        }

        constructor(r, g, b, name) {
            this.r = r;
            this.g = g;
            this.b = b;

            this.name = name;
        }

        get mono () {
            return 0.2125 * this.r + 0.7154 * this.g + 0.0721 * this.b;
        }

        get hex() {
            const chunks = [
                ('0' + this.r.toString(16)).slice(-2),
                ('0' + this.g.toString(16)).slice(-2),
                ('0' + this.b.toString(16)).slice(-2),
            ]
            return `#${chunks.join('')}`.toUpperCase();
        }

        distance(other_color) {
            const distance = {
                r: Math.abs(this.r - other_color.r),
                g: Math.abs(this.g - other_color.g),
                b: Math.abs(this.b - other_color.b),
                mono: Math.abs(this.mono - other_color.mono),
            };

            return Math.sqrt(
                Math.pow(distance.r, 2) +
                Math.pow(distance.g, 2) +
                Math.pow(distance.b, 2) +
                Math.pow(distance.mono, 2)
            );
        }
    }

    /**
     * Convert hex color to rgb (with optional monochrome value)
     * @param   hex         Hex color
     * @param  includeMono Include monochrome value
     * @return Object
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
     * @param  color0 RGB color
     * @param  color1 RGB color
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
        const style = getComputedStyle(document.body);

        let presentColors = [];

        // Get all css color variables
        // (see https://stackoverflow.com/a/54851636/982902)
        const colors = Array.from(document.styleSheets).filter(sheet => {
            return sheet.href === null || sheet.href.startsWith(window.location.origin);
        }).reduce((allVars, sheet) => {
            const rules = Array.from(sheet.cssRules).filter(rule => rule.selectorText === ':root');
            const colors = rules.reduce((vars, rule) => {
                const styles = Array.from(rule.style).filter(name => name.startsWith('--'));
                const colors = styles.reduce((defs, name) => {
                    name = name.trim();
                    const color = style.getPropertyValue(name).trim();
                    if (
                        color.match(/^#[0-9a-f]{6}$/i)
                        && !presentColors.includes(name)
                    ) {
                        defs.push(Color.fromHexValue(color, name.slice(2)));
                        presentColors.push(name);
                    }
                    return defs;
                }, [])
                return vars.concat(...colors);
            }, [])
            return allVars.concat(...colors);
        }, []);

        // Create vue app
        const appConfig = {
            el: document.getElementById('color-list'),
            data: {
                colors: colors,
                searchColor: false
            },
            methods: {
                copy(event) {
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
                sortedColors() {
                    if (!this.searchColor) {
                        return this.colors;
                    }
                    const distances = this.colors.map((color, index) => {
                        return {
                            distance: color.distance(this.searchColor),
                            index: index
                        };
                    });
                    distances.sort((a, b) => {
                        return a.distance - b.distance;
                    });

                    return distances.reduce((colors, item) => {
                        colors.push(this.colors[item.index]);
                        return colors;
                    }, []);
                }
            }
        };

        var promise;
        if (window.Vue) {
            promise = Promise.resolve(new Vue(appConfig));
        } else if (window.STUDIP.Vue) {
            promise = STUDIP.Vue.load().then(({createApp}) => {
                return createApp(appConfig);
            })
        } else {
            promise = Promise.reject('No search possible due to missing vue');
        }

        promise.then(app => {
            // Attach search input from sidebar
            const search = document.querySelector('.sidebar,#sidebar').querySelector('input[name="search-color"]');
            search.addEventListener('keyup', event => {
                if (search.checkValidity()) {
                    app.searchColor = Color.fromHexValue(search.value);
                } else {
                    app.searchColor = false;
                }
            });
            search.closest('form').addEventListener(
                'submit',
                event => event.preventDefault()
            );
        });
    });
}());
