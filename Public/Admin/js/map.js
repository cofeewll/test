/**
 * Created by Administrator on 2017/7/10 0010.
 */
var map = new BMap.Map("container");
map.centerAndZoom("郑州", 12);
map.enableScrollWheelZoom(true);    //启用滚轮放大缩小，默认禁用
//map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用

// 添加带有定位的导航控件
var navigationControl = new BMap.NavigationControl({
    // 靠左上角位置
    anchor: BMAP_ANCHOR_TOP_LEFT,
    // LARGE类型
    type: BMAP_NAVIGATION_CONTROL_LARGE,
    // 启用显示定位
    enableGeolocation: true
});
map.addControl(navigationControl);
var localSearch = new BMap.LocalSearch(map);
localSearch.enableAutoViewport(); //允许自动调节窗体大小

//显示地址信息窗口
function showLocationInfo(pt, rs,marker){
    var opts = {
        width : 250,     //信息窗口宽度
        height: 100,     //信息窗口高度
        title : ""  //信息窗口标题
    }
    var addComp = rs.addressComponents;
    var addr = "当前位置：" + addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber + "<br/>";
    addr += "纬度: " + pt.lat + ", " + "经度：" + pt.lng;
    //alert(addr);
    document.getElementById("sLat").value=pt.lat;
    document.getElementById("sLng").value=pt.lng;
    var infoWindow = new BMap.InfoWindow(addr, opts);  //创建信息窗口对象
    marker.openInfoWindow(infoWindow);
}
function searchByStationName() {
    map.clearOverlays();//清空原来的标注
    var keyword = document.getElementById("text_").value;
    localSearch.setSearchCompleteCallback(function (searchResult) {
        var poi = searchResult.getPoi(0);
        document.getElementById("sLng").value = poi.point.lng ;
        document.getElementById("sLat").value =  poi.point.lat;
        map.centerAndZoom(poi.point, 13);
        var marker = new BMap.Marker(new BMap.Point(poi.point.lng, poi.point.lat));  // 创建标注，为要查询的地方对应的经纬度
        map.addOverlay(marker);
        var content = document.getElementById("text_").value + "<br/><br/>经度：" + poi.point.lng + "<br/>纬度：" + poi.point.lat;
        var infoWindow = new BMap.InfoWindow("<p style='font-size:14px;'>" + content + "</p>");
        marker.addEventListener("click", function () { this.openInfoWindow(infoWindow); });
        marker.enableDragging();           // 可拖拽
        var gc = new BMap.Geocoder();//地址解析类
        //添加标记拖拽监听
        marker.addEventListener("dragend", function(e){
            //获取地址信息
            gc.getLocation(e.point, function(rs){
                showLocationInfo(e.point, rs,marker);
            });
        });

        //添加标记点击监听
        marker.addEventListener("click", function(e){
            gc.getLocation(e.point, function(rs){
                showLocationInfo(e.point, rs,marker);
            });
        });
        // marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
    });
    localSearch.search(keyword);
} 