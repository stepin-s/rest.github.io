/**
 * Хранилище JS Данных между обнволениями страниц
 * @returns {TaxiStore.Anonym$3}
 */
function TaxiDataStore()
{
    var self = {};
    self.get = function(key) {
        var raw = $.cookie(key);
        if (raw) {
            return JSON.parse(raw);
        } else {
            return null;
        }
    };
    self.set = function(key, value) {
        $.cookie(key, JSON.stringify(value),
                {
                    expires: 365,
                    path: "/"
                });
    };
    return self;
}


