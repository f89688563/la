import { Component, OnInit } 	from '@angular/core';
import { HttpClient }			from '@angular/common/http';
// import { Http, Response, Headers, RequestOptions,URLSearchParams } from '@angular/http';

import { Observable }			from 'rxjs/Rx';

import 'rxjs/add/operator/toPromise';

interface ItemsResponse {
	lists: string[];
}

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.scss']
})
export class IndexComponent implements OnInit {

	data: any[];
	goodsRec: any[];
	goodsHot: Observable<any[]>;
  	constructor(public http: HttpClient) { }

  	ngOnInit() {
  		this.loadAd();
  		this.loadRec();
  		this.loadHot();
  	}

  	loadAd() {
  		let url = 'wapi/ad';
  		this.http.get<ItemsResponse>(url).subscribe( data => {
  			this.data = data['lists'];
  		} );
  	}

  	loadRec() {
  		let url = 'wapi/goods/rec';
  		return this.http.get(url)
  			.subscribe( data => {
	  			this.goodsRec = data['lists'];
	  		} );
  	}

  	loadHot(){
  		let url = 'wapi/goods/hot';
  		return this.http.get(url)
  			.subscribe( data => {
	  			this.goodsHot = data['lists'];
	  		} );
  	}

}
