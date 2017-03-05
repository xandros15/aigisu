/**
 * Created by xandros15 on 2016-11-19.
 */
(function () {
    var storage = {
        units: [],
        filter: {
            missing_cg: false,
            rarity: 'all',
            name: '',
            server: 'all',
        },
        sort: {
            ident: 'id',
            order: 'desc',
        },
    };
    var template = _.template(document.getElementById('unit-template').innerHTML);
    axios.get(API.UNITS + '?expand=missing_cg,cg').then(function (response) {
        storage.units = response.data;
        updateUnitList();
    });

    document.getElementById('filter-rarities').addEventListener('change', function (e) {
        updateFilter('rarity', e.target.value);
    });

    document.getElementById('filter-name').addEventListener('input', debounce(function (e) {
        updateFilter('name', e.target.value)
    }, 300));

    document.getElementById('filter-missing-cg').addEventListener('change', function (e) {
        updateFilter('missing_cg', e.target.checked)
    });

    document.getElementById('filter-server').addEventListener('change', function (e) {
        updateFilter('server', e.target.value)
    });

    document.getElementById('sort-units').addEventListener('change', function (e) {
        const ident = e.target.value == 'created_at' ? 'id' : e.target.value;
        const order = ident == 'id' ? 'desc' : 'asc';
        updateSort({ident: ident, order: order})
    });

    function updateSort(sort) {
        storage.sort = sort;
        updateUnitList();
    }

    function updateFilter(name, value) {
        storage.filter[name] = value;
        updateUnitList();
    }

    function debounce(callback, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) {
                    callback.apply(context, args);
                }
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                callback.apply(context, args);
            }
        };
    }

    function updateUnitList() {
        var newUnits = Object.assign({}, storage.units);
        console.log(storage);
        var filter = storage.filter;
        newUnits = _.filter(newUnits, function (unit) {
            var rarityFilter = filter.rarity == 'all' || filter.rarity == unit.rarity;
            var nameFilter = filter.name.length == 0 || unit.name.toLowerCase().indexOf(filter.name.toLowerCase()) != -1;
            var missingCGFilter = !filter.missing_cg || unit.missing_cg.length > 0;
            var serverFilter = filter.server == 'all' || unit[filter.server];

            return rarityFilter && nameFilter && missingCGFilter && serverFilter;
        });

        newUnits = _.orderBy(newUnits, storage.sort.ident, storage.sort.order);

        document.getElementById('units-index').innerHTML = template({
            units: newUnits
        });
    }
})();