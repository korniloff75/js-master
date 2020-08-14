'use strict';

var PLANETS_SEQUENCE = ['Sun','Moon','Mercury','Venus','Mars','Jupiter','Saturn','Uranus','Neptune','Pluto',],
	// *Данные из БД в последовательности PLANETS_SEQUENCE
	_NATAL = [73.7827109618, 101.763008923, 94.2913378743, 89.0762641247, 59.4997151133, 180.54853596, 182.984480102, 237.430854158, 263.766604795, 201.740985129,,],
	NATAL_DELTA= [],
	OPPONENTS_ANGLES= {},
	// TSS_KEYS,
	// PRE,
	CONTROL_ANGLES = [0,60,90,120,180];

export async function init (konva) {
	// TSS_KEYS = konva.TSS_KEYS;
	// PRE = konva.PRE;

	Object.assign(window,konva);

	calculateOpponentsAngles();

	// var request = sendRequest();
	var request = await sendRequest(),
		response = await request.text();
	console.log(
		// 'request= ', request,
		// 'request.json= ', await request.json(),
		// 'request.text= ', response,
		'OPPONENTS_ANGLES= ', OPPONENTS_ANGLES,
	);

	PRE.innerHTML += response;

}

// todo Определить время прохождения контрольных углов

/**
 *
 */
function calculateOpponentsAngles () {
	_NATAL.forEach((i,ind)=>{
		var name = PLANETS_SEQUENCE[ind];

		/* var opponentNames = PLANETS_SEQUENCE.slice(0),
		opponentVals = NATAL.slice(0);

		// *Убираем текущий элемент
		opponentNames.splice(ind,1);
		opponentVals.splice(ind,1); */

		// *Вычисляем OPPONENTS_ANGLES

		OPPONENTS_ANGLES[name] = CONTROL_ANGLES.reduce((acc, controlA)=>{
			var min= i - controlA,
			plus= i + controlA;

			// if(plus>360) console.log('plus= ', plus);

			acc[controlA]= Array.from(new Set([
				min<0? 360+min:min,
				plus>360? plus-360: plus
			])); //.filter((a,ind,arr)=> !arr.includes(a));
			return acc;
		}, {});

		/* NATAL_OBJ[name] = opponentVals.reduce((acc,val,indVal)=>{
			acc[opponentNames[indVal]] = CONTROL_ANGLES.reduce((acc, controlA)=>{
				acc[controlA]= [
					Math.abs(i - controlA),
					i + controlA
				].map(i=> i > 180 ? 360 - i : i);
				return acc;
			}, {});
			return acc;
		}, {}); */

		/* console.log(
			'name= ', name,
			'opponents= ', opponentNames,
		); */

	}); //*NATAL.forEach
}

async function sendRequest () {
	return await fetch('./php/fetchHandler.class.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify(OPPONENTS_ANGLES)
	});
}