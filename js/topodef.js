/*
拓扑定义：EasyUI样式和数据
 */

var addState = false;
var modifyState = false;
var inRowState = false;
var topoType = '';
var topoLevel = '';
var thisRow = {};

function empty() {

	$('#topoType').val(' ');
	$('#topoLevel').val(' ');
	$('#topoDescr').val(' ');
	$('#iconType').val(' ');
	$('#linkType').val(' ');
	$('#nodeConvType').val(' ');
	$('#linkConvType').val(' ');

}

function disableAll() {
	$('#topoType').attr('disabled', 'disabled');
	$('#topoLevel').combobox('disable');
	$('#topoDescr').attr('disabled', 'disabled');
	$('#iconType').combobox('disable');
	$('#linkType').combobox('disable');
	$('#nodeConvType').combobox('disable');
	$('#linkConvType').combobox('disable');
}

function enableAll() {
	$('#topoType').attr('disabled', false);
	$('#topoLevel').combobox('enable');
	$('#topoDescr').attr('disabled', false);
	$('#iconType').combobox('enable');
	$('#linkType').combobox('enable');
	$('#nodeConvType').combobox('enable');
	$('#linkConvType').combobox('enable');
}

function init() {
	disableAll();
	$('#add').linkbutton('enable');
	$('#modify').linkbutton('enable');
	$('#delete').linkbutton('enable');
	$('#save').linkbutton('disable');
	$('#add').linkbutton({
		text: '新增',
		iconCls: 'icon-add'
	});
	$('#modify').linkbutton({
		text: '修改',
		iconCls: 'icon-edit'
	});
	addState = false;
	modifyState = false;
	inRowState = false;
}

window.onload = function() {
	disableAll();
}

$(function() {

	$('#datalist').datagrid({

		title: 'topo定义列表',
		width: 715,
		height: 600,
		fitColumns: true,
		striped: true,
		singleSelect: true,
		url: '../ajax.php?c=TopoDefCtrl&a=getAllTopoDefForMain',
		columns: [
			[{
				field: 'topo_type',
				title: '拓扑类型',
				width: 50,
				align: 'center'
			}, {
				field: 'topo_level',
				title: '拓扑级别',
				width: 50,
				align: 'center'
			}, {
				field: 'descr',
				title: '拓扑描述',
				width: 50,
				align: 'center'
			}, {
				field: 'nodeicontype',
				title: '图标显示类型',
				width: 50,
				align: 'center'
			}, {
				field: 'linklinetype',
				title: '链路显示类型',
				width: 50,
				align: 'center'
			}, {
				field: 'nodeconvtype',
				title: '节点聚合类型',
				width: 50,
				align: 'center'
			}, {
				field: 'linkconvtype',
				title: '链路聚合类型',
				width: 50,
				align: 'center'
			}]
		],

		onClickRow: function(index, row) {

			thisRow.topo_type = row.topo_type;
			thisRow.topo_level = row.topo_level;
			thisRow.descr = row.descr;
			thisRow.nodeicontype = row.nodeicontype;
			thisRow.linklinetype = row.linklinetype;
			thisRow.nodeconvtype = row.nodeconvtype;
			thisRow.linkconvtype = row.linkconvtype;

			init();
			$('#topoType').val(row.topo_type);
			$('#topoLevel').combobox('setValue', row.topo_level);
			$('#topoDescr').val(row.descr);
			$('#iconType').combobox('setValue', row.nodeicontype);
			$('#linkType').combobox('setValue', row.linklinetype);
			$('#nodeConvType').combobox('setValue', row.nodeconvtype);
			$('#linkConvType').combobox('setValue', row.linkconvtype);
			disableAll();
			inRowState = true;
			topoLevel = row.topo_level;
			topoType = row.topo_type;

		}

	});

	$('#data-detail').panel({

		title: '拓扑定义细节',
		width: 520,
		height: 600,
	});

	$('#topoLevel').combobox({

		valueField: 'id',
		textField: 'topo_level',
		editable: false,
		data: [{
			topo_level: '全国拓扑',
			id: 00
		}, {
			topo_level: '省级拓扑',
			id: 01
		}, {
			topo_level: '本地网拓扑',
			id: 02
		}],
		width: 234,
		height: 29,
		editable: false,

	});

	$('#iconType').combobox({

		valueField: 'value',
		textField: 'iconType',
		data: [{
			iconType: '厂商图标',
			value: 1
		}, {
			iconType: '示例图标',
			value: 0
		}],
		editable: false,
		width: 234,
		height: 29,

	});

	$('#linkType').combobox({

		valueField: 'value',
		textField: 'linkType',
		data: [{
			linkType: '链路带宽',
			value: 1
		}, {
			linkType: '链路接口',
			value: 0
		}],
		editable: false,
		width: 234,
		height: 29,

	});

	$('#nodeConvType').combobox({

		valueField: 'value',
		textField: 'nodeConvType',
		data: [{
			nodeConvType: '逻辑节点',
			value: 1
		}, {
			nodeConvType: '物理节点',
			value: 0
		}],
		editable: false,
		width: 234,
		height: 29,

	});

	$('#linkConvType').combobox({

		valueField: 'value',
		textField: 'linkConvType',
		data: [{
			linkConvType: '聚合显示',
			value: 1
		}, {
			linkConvType: '物理链路',
			value: 0
		}],
		editable: false,
		width: 234,
		height: 29,

	});

	$('#add').linkbutton({

		width: 66,
		height: 40,
		iconCls: 'icon-add',

		onClick: function() {

			addState = !addState;


			if (addState) {
				$('#add').linkbutton({
					text: '放弃',
					iconCls: 'icon-lock'
				});
				$('#empty').linkbutton('enable');
				$('#modify').linkbutton('disable');
				$('#delete').linkbutton('disable');
				$('#save').linkbutton('enable');

				enableAll();
				empty();

			} else {
				$('#add').linkbutton({
					text: '新增',
					iconCls: 'icon-add'
				});
				$('#empty').linkbutton('disable');
				$('#modify').linkbutton('enable');
				$('#delete').linkbutton('enable');
				$('#save').linkbutton('disable');

				disableAll();

				$('#topoType').val(thisRow.topo_type);
				$('#topoLevel').combobox('setValue', thisRow.topo_level);
				$('#topoDescr').val(thisRow.descr);
				$('#iconType').combobox('setValue', thisRow.nodeicontype);
				$('#linkType').combobox('setValue', thisRow.linklinetype);
				$('#nodeConvType').combobox('setValue', thisRow.nodeconvtype);
				$('#linkConvType').combobox('setValue', thisRow.linkconvtype);
			}
		}
	});

	$('#modify').linkbutton({

		width: 66,
		height: 40,
		iconCls: 'icon-edit',

		onClick: function() {

			modifyState = !modifyState;

			if (!inRowState) {
				$.messager.alert('Tips', '请选择一条数据进行操作！');
			}

			if (inRowState) {
				if (modifyState) {
					$('#modify').linkbutton({
						text: '放弃',
						iconCls: 'icon-lock'
					});
					$('#empty').linkbutton('enable');
					$('#add').linkbutton('disable');
					$('#delete').linkbutton('disable');
					$('#save').linkbutton('enable');

					enableAll();

				} else {
					$('#modify').linkbutton({
						text: '修改',
						iconCls: 'icon-edit'
					});
					$('#empty').linkbutton('disable');
					$('#add').linkbutton('enable');
					$('#delete').linkbutton('enable');
					$('#save').linkbutton('disable');

					disableAll();
				}
			}
		}
	});

	$('#delete').linkbutton({

		width: 66,
		height: 40,
		iconCls: 'icon-remove',

		onClick: function() {

			$.messager.confirm('WARNING', '确定删除？', function(flag) {
				if (flag) {
					$.ajax({
						type: 'post',
						url: '../ajax.php',
						data: {
							'c': 'TopoDefCtrl',
							'a': 'delTopoDef',
							'topo_type': $('#topoType').val(),
							'topo_level': $('#topoLevel').combobox('getText')
						},
						dataType: 'json',
						success: function(data) {

							if (data.success) {
								$.messager.alert('Tips', '删除拓扑定义成功！');
								$('#datalist').datagrid('reload');
							} else {
								$.messager.alert('Tips', '删除拓扑定义失败！请重试！');
							}

						}
					});
				}
			})

			init();
		}

	});

	$('#empty').linkbutton({

		width: 66,
		height: 40,
		iconCls: 'icon-clear',
		disabled: true,


		onClick: function() {

			empty();

		}

	});

	$('#save').linkbutton({

		width: 66,
		height: 40,
		iconCls: 'icon-save',
		disabled: true,

		onClick: function() {

			if (addState === true) {
				$.ajax({
					type: 'post',
					url: '../ajax.php',
					data: {
						'c': 'TopoDefCtrl',
						'a': 'addTopoDef',
						'topo_type': $('#topoType').val(),
						'topo_level': $('#topoLevel').combobox('getText'),
						'descr': $('#topoDescr').val(),
						'nodeicontype': $('#iconType').combobox('getText'),
						'linklinetype': $('#linkType').combobox('getText'),
						'nodeconvtype': $('#nodeConvType').combobox('getText'),
						'linkconvtype': $('#linkConvType').combobox('getText')
					},
					dataType: 'json',
					success: function(data) {

						if (data.success) {
							$.messager.alert('Tips', '新增拓扑定义成功！');
							$('#datalist').datagrid('reload');
						} else if (data.msg === 'exist') {
							$.messager.alert('Tips', '该拓扑定义已存在！');
						} else {
							$.messager.alert('Tips', '新增拓扑定义失败！请重试！');
						}
					}
				});
			} else if (modifyState === true) {
				$.ajax({
					type: 'post',
					url: '../ajax.php',
					data: {
						'c': 'TopoDefCtrl',
						'a': 'updateTopoDef',
						'topo_type': $('#topoType').val(),
						'topo_level': $('#topoLevel').combobox('getText'),
						'descr': $('#topoDescr').val(),
						'nodeicontype': $('#iconType').combobox('getText'),
						'linklinetype': $('#linkType').combobox('getText'),
						'nodeconvtype': $('#nodeConvType').combobox('getText'),
						'linkconvtype': $('#linkConvType').combobox('getText'),
						'topoType': topoType,
						'topoLevel': topoLevel
					},
					dataType: 'json',
					success: function(data) {

						if (data.success) {
							$.messager.alert('Tips', '修改拓扑定义成功！');
							$('#datalist').datagrid('reload');
						} else if (data.msg === 'exist') {
							$.messager.alert('Tips', '该拓扑定义已存在！');
						} else {
							$.messager.alert('Tips', '修改拓扑定义失败！请重试！');
						}

					}
				});
			}

			init();

		}

	});

});