/**
 * Created by xandros15 on 2016-11-19.
 */
(function () {
    const storage = {
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

    const template = _.template(document.getElementById('unit-template').innerHTML);

    const initStyleImages = () => {
        const style = document.createElement('style');
        style.id = 'icons-stylesheet';
        document.head.appendChild(style);
    };

    const addImageToStyle = (unit) => {
        document.getElementById('icons-stylesheet').innerHTML +=
            '#unit-' + unit.id + ' .icon-img{background-image: url(\'' + unit.icon + '\');} ';
    };

    const addImagesToStyle = (units) => {
        _.each(units, addImageToStyle);
    };

    const updateSort = (sort) => {
        storage.sort = sort;
        updateUnitList();
    };

    const updateFilter = (name, value) => {
        storage.filter[name] = value;
        updateUnitList();
    };

    const debounce = (callback, wait, immediate) => {
        let timeout;
        return () => {
            const context = this, args = arguments;
            const later = () => {
                timeout = null;
                if (!immediate) {
                    callback.apply(context, args);
                }
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                callback.apply(context, args);
            }
        };
    };

    const updateUnitList = () => {
        let newUnits = Object.assign({}, storage.units);
        const filter = storage.filter;
        newUnits = _.filter(newUnits, (unit) => {
            const rarityFilter = filter.rarity == 'all' || filter.rarity == unit.rarity;
            const nameFilter = filter.name.length == 0 || unit.name.toLowerCase().indexOf(filter.name.toLowerCase()) != -1;
            const missingCGFilter = !filter.missing_cg || unit.missing_cg.length > 0;
            const serverFilter = filter.server == 'all' || unit[filter.server];

            return rarityFilter && nameFilter && missingCGFilter && serverFilter;
        });

        newUnits = _.orderBy(newUnits, storage.sort.ident, storage.sort.order);

        document.getElementById('units-index').innerHTML = template({
            units: newUnits
        });
    };

    const bootstrap = () => {
        document.getElementById('filter-rarities')
            .addEventListener('change', (e) => updateFilter('rarity', e.target.value));
        document.getElementById('filter-name')
            .addEventListener('input', (e) => debounce(updateFilter('name', e.target.value), 300));

        document.getElementById('filter-missing-cg')
            .addEventListener('change', (e) => updateFilter('missing_cg', e.target.checked));

        document.getElementById('filter-server').addEventListener('change', (e) => updateFilter('server', e.target.value));

        document.getElementById('sort-units').addEventListener('change', (e) => {
            const ident = e.target.value == 'created_at' ? 'id' : e.target.value;
            const order = ident == 'id' ? 'desc' : 'asc';
            updateSort({ident: ident, order: order})
        });

        axios.get(API.UNITS + '?expand=missing_cg,cg').then((response) => {
            storage.units = response.data;
            initStyleImages();
            addImagesToStyle(storage.units);
            updateUnitList();
        });

        return this;
    };

    return bootstrap();
})();