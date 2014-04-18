function InputHistory() {
    this.history = [];
	this.position = 0;
    this.getNext = function() {
		if (this.history.length == 0) {
			return ''
		}
	
		this.position++
		if (this.position > this.history.length - 1) {
			this.position = this.history.length
			return ''
		} else {
			return this.history[this.position]
		}
    };
	this.getPrevious = function() {
		if (this.history.length == 0) {
			return ''
		}
	
		this.position--
		if (this.position < 0 ) {
			this.position = -1
			return ''
		} else {
			return this.history[this.position]
		}
	};
	this.addHistory = function(newHistory) {
		// don't add duplicates to the history
		if (this.history[this.history.length - 1] != newHistory) {
			this.history.push(newHistory)
		}
		this.position = this.history.length
	}
}