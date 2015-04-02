var frApp = angular.module('frApp', ['ui.bootstrap']);

frApp.controller('FormController', ['$scope', '$attrs', '$element', '$modal', '$http', function($scope, $attrs, $element, $modal, $http) {

	$scope.data = new Object();
	$scope.data.telEmail = "";
	$scope.data.tel;
	$scope.data.action = $attrs.action;
	$scope.invalid = false;
  	
	$scope.editTel = function( tel )
	{
		return (tel.replace(/\D/g, "")).substr(1);  
	};

	$scope.openModal = function( header, text )
	{
		var modal = $modal.open({
			controller: 'ModalFormController',
			template: '<div class="modal-header">' +
			'<h3 class="modal-title">'+ header +'</h3>' +
			'</div>' +
			'<div class="modal-body">' +
			'<p>'+ text +'</p>' +
			'</div>' +
        		'<div class="modal-footer">' +
            		'<button class="btn btn__slide btn_ok" ng-click="ok()">OK</button>' +
        		'</div>',									
			size: 'lg'
		});
	}
    
	$scope.submit = function()
	{
		if($scope.data.telEmail != "")
		{
			$scope.invalid = false;
			$scope.data.tel = $scope.editTel($scope.data.telEmail);
          		$http.post('../handler.php', $scope.data)
				.success(function(data, status, headers, config) {
					if(data == 1)
					{
						$scope.data.telEmail = "";
						$scope.openModal("Спасибо!", "Ваша заявка зарегистрирована.");
					}					
					else
						$scope.openModal("Ошибка. Приносим свои извинения", "Попробуйте позже.");
				})
				.error(function() {
					console.log("it's not good");
				});          
		}
		else
			$scope.invalid = true;
	};
    
	$element.on("submit", function(e) 
	{
		e.preventDefault();
		$scope.submit();
	});
}]);

frApp.controller('ModalFormController', function($scope, $modalInstance) {

	$scope.ok = function()
	{
		$modalInstance.dismiss();
	};
});
