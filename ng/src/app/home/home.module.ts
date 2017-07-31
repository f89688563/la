import { NgModule }                   from '@angular/core';
import { CommonModule }               from '@angular/common';
import { FormsModule }                from '@angular/forms';
import { HttpClientModule }           from '@angular/common/http';


import { WeUiModule }                 from 'ngx-weui';

import { IndexComponent }             from './index/index.component';
import { SwiperComponent }            from './common/swiper/swiper.component';
import { GoodsItemComponent }         from './goods/goods-item/goods-item.component';
import { GoodsComponent }             from './goods/goods/goods.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    HttpClientModule,
    WeUiModule
  ],
  declarations: [
  	IndexComponent,
  	SwiperComponent,
  	GoodsItemComponent,
  	GoodsComponent
  ],
  exports: [
  	IndexComponent,
  	SwiperComponent
  ],
  providers: [
  ]
})
export class HomeModule { }
