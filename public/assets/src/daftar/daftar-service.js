/**
 * Created by - LENOVO - on 24/08/2015.
 */
app.factory('daftar', ['$http', function ($http) {
    return {
        // get data dengan pagination dan pencarian data
        get: function (page, term) {
            return $http({
                method: 'get',
                url: '/api/daftar?page=' + page + '&term=' + term,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}
            });
        },

        getLastdaftar: function () {
            return $http({
                method: 'get',
                url: '/api/get-last-daftar',
            });
        },

        //Simpan data
        store: function (inputData) {
            return $http({
                method: 'POST',
                url: '/api/daftar',
                data: $.param(inputData)
            });
        },

        //Tampil Data
        show: function (_id) {
            return $http({
                method: 'get',
                url: '/api/daftar/' + _id,
            });
        },

        destroy: function (_id) {
            return $http({
                method: 'delete',
                url: '/api/daftar/' + _id,
            });
        },

 updatepassword: function (inputData) {  
            return $http({  
                method: 'put',  
                url: 'api/updatePass-daftar/',  
                data: $.param(inputData)  
            });  
        },  
        //Update data
        update: function (inputData) {
            return $http({
                method: 'put',
                url: '/api/daftar/' + inputData.id,
                data: $.param(inputData)
            });
        },
        kunci: function (_id) {
            return $http({
                method: 'put',
                url: '/api/kunci-daftar/' + _id
            });
        },

    }
}]);