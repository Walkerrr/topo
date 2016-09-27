/**
 * Created by liujf on 2016/8/23
 * Version 1.0
 */

var expandState = false;

function RegionSelectWin(areaId, width, height) {

	this._areaId = areaId || '';
	this._width = width || 250;
	this._height = height || 330;
	this.key_areaobj = {};

	//在body节点下创建窗口节点
	var divLabel = document.createElement('div');
	divLabel.id = "winTestArea";
	var tableLabel = document.createElement('table');
	tableLabel.id = "regiontreeKey"
	tableLabel.style.width = this._width;
	tableLabel.style.height = this._height;
	divLabel.appendChild(tableLabel);

	if (this._areaId) {
		document.getElementById(this._areaId).appendChild(divLabel);
	} else {
		document.body.appendChild(divLabel);
	}
}

RegionSelectWin.prototype = {

	//初始化区域选择框中的treegrid
	initRegion: function(url, callback) {

		that = this;

		var rgntreedef =
			[
				[{
					"field": "text",
					"title": "区域名称",
					"width": 248,
					"align": "left"
				}, {
					"field": "id",
					"title": "区域编码",
					"width": 0,
					"align": "center",
					"hidden": true
				}]
			];
		url = "./../ajax.php?c=CommTree&a=areaTree&id=00&maxlevel=3";
		$("#regiontreeKey").treegrid({
			url: url,
			method: 'post',
			singleSelect: true,
			width: 258,
			height: 333,
			lines: true,
			rownumbers: false,
			idField: 'id',
			treeField: 'text',
			singleSelect: true,
			columns: rgntreedef,
			pagination: false,
			//onDblClickRow:this.onDoubleClickRowKeyArea

			onBeforeExpand: function() {

				$('#regiontreeKey').treegrid("collapseAll");

			},

			onExpand: function(row) {

				var id = row.id;
				$('#province').html(row.text);
				expandState = true;

			},

			onCollapse: function(row) {

				expandState = false;

			},

			onDblClickRow: function(row) {

				if (expandState) {
					if (row.cityid === "*") {
						$('#province').html(row.text);
						$('#city').html("*");
					} else {
						$('#city').html(row.text);
					}
				} else {
					$('#province').html(row.text);
					$('#city').html("*");
				}
				that.closeRegionWin();
				var id = row.id;
				that.key_areaobj.provinceid = row.provinceid;
				that.key_areaobj.cityid = row.cityid;
				that.key_areaobj.conrgnid = row.conrgnid;
				that.key_areaobj.intesrvrgnid = row.intesrvrgnid;

				if (callback) {
					callback.call(this, that.key_areaobj);
				}
			}
		});

		this.initRegionWin();
	},
	//初始化windows弹框
	initRegionWin: function() {

		$('#winTestArea').window({
			width: 258,
			height: 358,
			title: "区域选择(鼠标双击完成选择)",
			collapsible: false,
			minimizable: false,
			maximizable: false,
		}).window("close");
	},
	//并打开windows弹框
	openRegionWin: function(left, top) {

		_left = left || 560;
		_top = top || 200;

		$('#winTestArea').window({
			left: _left,
			top: _top,
			modal: true
		}).window("open");
	},
	//关闭windows弹框
	closeRegionWin: function() {
		$('#winTestArea').window("close");
	},
	//双击区域名触发事件
	onDoubleClickRowKeyArea: function(rowData) {

		var id = rowData.id;
		this.key_areaobj.provinceid = rowData.provinceid;
		this.key_areaobj.cityid = rowData.cityid;
		this.key_areaobj.conrgnid = rowData.conrgnid;
		this.key_areaobj.intesrvrgnid = rowData.intesrvrgnid;
	},
	//返回单个区域节点信息
	getKeyAreaData: function() {
		return this.key_areaobj;
	}
}