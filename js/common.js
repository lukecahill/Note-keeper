/**
* @function String format
*
* Add functionality to String object, for C# style string formatting.
* Usage: "{0} is dead, but {1} is alive! {0} {2}".format("ASP", "ASP.NET")
* From; http://stackoverflow.com/a/4673436
**/
if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) { 
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}

/**
* @function localStorageTest
*
* Checks that the localStorage is enabled on the users browser.
**/
function localStorageTest() {
	var test = 'test';
	try {
		localStorage.setItem(test, test);
		localStorage.removeItem(test);
		return true;
	} catch(e) {
		return false;
	}
}