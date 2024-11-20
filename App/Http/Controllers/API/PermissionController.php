<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Asegúrate de importar la clase Response si la usas

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        // Aquí puedes agregar la lógica para retornar los permisos
        return response()->json([
            'message' => 'List of permissions'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        // Lógica para crear un nuevo permiso
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Agrega más reglas de validación según sea necesario
        ]);

        // Aquí puedes crear el permiso usando el modelo Permission (por ejemplo)
        // $permission = Permission::create($validated);

        return response()->json([
            'message' => 'Permission created successfully',
            // 'permission' => $permission
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): Response
    {
        // Lógica para mostrar un permiso por su ID
        // $permission = Permission::findOrFail($id);

        return response()->json([
            'message' => 'Permission details',
            // 'permission' => $permission
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): Response
    {
        // Validar y actualizar el permiso
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Agrega más reglas de validación según sea necesario
        ]);

        // $permission = Permission::findOrFail($id);
        // $permission->update($validated);

        return response()->json([
            'message' => 'Permission updated successfully',
            // 'permission' => $permission
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        // Lógica para eliminar un permiso
        // $permission = Permission::findOrFail($id);
        // $permission->delete();

        return response()->json([
            'message' => 'Permission deleted successfully'
        ], 200);
    }
}
