@extends('principal')

@section('contenido')

@if(Auth::check())
<notificacion-component v-if="menu==13" :notifications="notificaciones"></notificacion-component>
@if(Auth::user()->idRol == 1)
<template v-if="menu==0">
    <dashboard-component></dashboard-component>
</template>

<template v-if="menu==1">
    <categoria-component></categoria-component>
</template>

<template v-if="menu==2">
    <articulo-component></articulo-component>
</template>

<template v-if="menu==3">
    <ingreso-component></ingreso-component>
</template>

<template v-if="menu==4">
    <proveedor-component></proveedor-component>
</template>

<template v-if="menu==5">
    <venta-component></venta-component>
</template>

<template v-if="menu==6">
    <cliente-component></cliente-component>
</template>

<template v-if="menu==7">
    <usuario-component></usuario-component>
</template>

<template v-if="menu==8">
    <rol-component></rol-component>
</template>

<template v-if="menu==9">
    <reporte_ingreso-component></reporte_ingreso-component>
</template>

<template v-if="menu==10">
    <reporte_venta-component></reporte_venta-component>
</template>

<template v-if="menu==11">
    <h1>Soy la vista ayuda</h1>
</template>

<template v-if="menu==12">
    <h1>Soy la vista aerca de</h1>
</template>
@elseif(Auth::user()->idRol == 2)
<template v-if="menu==0">
    <dashboard-component></dashboard-component>
</template>
<template v-if="menu==5">
    <venta-component></venta-component>
</template>

<template v-if="menu==6">
    <cliente-component></cliente-component>
</template>

<template v-if="menu==10">
    <reporte_venta-component></reporte_venta-component>
</template>

<template v-if="menu==11">
    <h1>Soy la vista ayuda</h1>
</template>

<template v-if="menu==12">
    <h1>Soy la vista aerca de</h1>
</template>
@elseif(Auth::user()->idRol == 3)
<template v-if="menu==9">
    <reporte_ingreso-component></reporte_ingreso-component>
</template>

<template v-if="menu==11">
    <h1>Soy la vista ayuda</h1>
</template>

<template v-if="menu==12">
    <h1>Soy la vista aerca de</h1>
</template>

<template v-if="menu==0">
    <dashboard-component></dashboard-component>
</template>

<template v-if="menu==1">
    <categoria-component></categoria-component>
</template>

<template v-if="menu==2">
    <articulo-component></articulo-component>
</template>

<template v-if="menu==3">
    <ingreso-component></ingreso-component>
</template>

<template v-if="menu==4">
    <proveedor-component></proveedor-component>
</template>
@endif
@endif
@endsection