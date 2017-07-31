import { Component, OnInit, ViewEncapsulation, Input } from '@angular/core';

@Component({
  	selector: 'app-swiper',
  	templateUrl: './swiper.component.html',
  	styleUrls: ['./swiper.component.scss'],
  	encapsulation: ViewEncapsulation.None
})
export class SwiperComponent implements OnInit {

    @Input() data: any[] = [
        { corver: 'assets/images/banner.jpeg', title: '标题1' }
    ];

    options: any = {effect: 'slide', spaceBetween: 30, autoplay: 2000};

  	constructor() { }

  	ngOnInit() {
    }

    navigateTo(id: number) {
      console.log(id)
    }

}
