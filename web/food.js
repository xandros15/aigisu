var data = [];

var app = {
    bootstrap: {
        run: function () {
            $.getJSON("food.json", function (respond) {
                data = respond;
                app.bootstrap.loadData();
                app.copy.add(false);
                $('.add').click(app.copy.add);
                $('.calc').click(app.calc.run);
            });
        },
        loadData: function () {
            var rarities = data.rarities;
            for (var i in rarities) {
                var element = document.createElement('option');
                element.setAttribute('value', rarities[i].id);
                element.innerHTML = rarities[i].name;
                $('.rarity select').append(element);
            }
        }
    },
    copy: {
        add: function (isNotFirstElement) {
            var item = document.createElement('li');

            item.innerHTML = document.getElementById('example').cloneNode(true).innerHTML;

            item.setAttribute('class', 'item');


            var removeButton = item.getElementsByClassName('remove').item(0);
            if (!isNotFirstElement) {
                item.getElementsByClassName('options').item(0).removeChild(removeButton);
            } else {
                item.getElementsByClassName('add').item(0).addEventListener('click', app.copy.add);
                removeButton.addEventListener('click', app.copy.remove);
            }

            document.getElementById('form-list').appendChild(item);
        },
        remove: function () {
            $(this).parents('.item').remove();
            app.nrOfItems--;
        }
    },
    calc: {
        run: function () {
            try {
                app.calc.setItems();
                app.calc.calcExp();
                app.gui.setData(app.calc.exp).parseData().showData();
            } catch (exception) {
                app.gui.setData(exception).showData();
            }
        },
        setItems: function () {
            app.calc.items = [];

            var items = document.getElementsByClassName('item');

            for (var i = 0; i < items.length; i++) {
                app.calc.items.push(app.calc.parseItem(items[i]));
            }
        },
        calcExp: function () {
            app.calc.exp = 0;
            var items = app.calc.items;
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                var exp = 0;
                var pattern = app.calc.findPattern(item.rarityId);
                exp += (item.isFemale && pattern.hasFemale) ? pattern.femaleBase : pattern.maleBase;
                exp += (item.isCCd) ? (data.cc.perLvl * (item.level - 1)) : (pattern.perLvl * (item.level - 1));
                exp += (item.specialBonus) ? data.special.bonus : 0;
                exp += (item.classBonus) ? pattern.classBonus : 0;
                exp *= item.count;
                app.calc.exp += exp;
            }
        },
        findPattern: function (id) {
            var rarities = data.rarities;
            for (var i = 0; i < rarities.length; i++) {
                if (rarities[i].id == id) {
                    return rarities[i];
                }
            }
            throw "Can't find pattern with id: " + id;
        },
        parseItem: function (item) {
            var defaults = {
                rarityId: 0,
                count: 0,
                level: 0,
                classBonus: false,
                specialBonus: false,
                isFemale: false,
                isCCd: false
            };

            var count = parseInt($(item).find(".count input").val());
            var level = parseInt($(item).find(".level input").val());

            if (!Number.isInteger(count)) {
                throw "Count must be int";
            }
            if (!Number.isInteger(level)) {
                throw "Level must be int";
            }

            defaults.rarityId = parseInt($(item).find(".rarity option:selected").val());
            defaults.count = count;
            defaults.level = level;
            defaults.classBonus = $(item).find(".class-bonus input").is(':checked');
            defaults.specialBonus = $(item).find(".special-bonus input").is(':checked');
            defaults.isFemale = $(item).find(".sex input").is(':checked');
            defaults.isCCd = $(item).find(".cc input").is(':checked');
            return defaults;
        },
        items: []
    },
    gui: {
        hookId: "results",
        data: "",
        showData: function () {
            document.getElementById(app.gui.hookId).innerHTML = app.gui.data;
            return app.gui;
        },
        setData: function (data) {
            app.gui.data = data;
            return app.gui;
        },
        parseData: function () {
            app.gui.data = app.gui.data + ' exp';
            return app.gui;
        }
    },
    exp: 0,
    nrOfItems: 0
};

$(document).ready(app.bootstrap.run);
