import _ from "lodash";
import MobileDetect from 'mobile-detect';

let detect = new MobileDetect(window.navigator.userAgent);

export function clientOnMobile() {
  return detect.mobile();
}

export function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

export function generateElementId() {
  return Math.random().toString(36).substr(2, 9);
}

export function arrayNextIndex(array, currentIndex, direction = "+", steps = 1) {
  if(direction !== "+" && direction !== "-") {
      throw new Error("Direction param must be + for forward or - for backward. Neither is being used");
  }

  const arrayLength = array.length;
	const indexIValue = array.indexOf(currentIndex);
	if(indexIValue == -1) {
    console.error(array);
    console.error("Index: " + currentIndex);
		throw new Error("Index given is not present in array.");
	}

	let nextIndex = false;

  if(direction == "+") {
    nextIndex = indexIValue + steps;
  }else{
    nextIndex = indexIValue - steps;
  }
    
    
  if(nextIndex >= arrayLength) {
    var remainder = nextIndex - arrayLength;
    return array[remainder];
  }
    
  
  if(nextIndex < 0) {
    var remainder = nextIndex + arrayLength;
    return array[remainder]
  }

	return array[nextIndex];
}