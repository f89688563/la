import { Component, OnInit, Input } from '@angular/core';

@Component({
  	selector: 'app-goods-item',
  	templateUrl: './goods-item.component.html',
  	styleUrls: ['./goods-item.component.scss']
})
export class GoodsItemComponent implements OnInit {

	@Input() item: any[];

  	constructor() { }

	ngOnInit() {
		
	}

}
